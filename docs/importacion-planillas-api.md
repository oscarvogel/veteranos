# Importación de planillas escaneadas (contrato v1)

Esta API recibe datos ya estructurados por Codex/IA. El backend **no hace OCR**: valida, concilia y recién después graba.

## Autenticación

Configurar fuera del repositorio:

- `API_PLANILLAS_KEY`: token enviado en `X-Planillas-Key` o `Authorization: Bearer ...`.
- `API_PLANILLAS_SECRET`: secreto usado para firmar el `confirm_token`.

## Flujo

1. POST `/api/planillas/preview` con el lote.
2. Revisar `errors`, `warnings` y `resolved`.
3. Si una advertencia fue revisada manualmente, reenviar ese elemento con `"revisado": true`.
4. Cuando `can_confirm=true`, conservar el `confirm_token`.
5. POST `/api/planillas/confirmar` con `payload` original y `confirm_token`.

La confirmación vuelve a ejecutar el preview y valida la firma antes de abrir la transacción.

## Ejemplo de payload

```json
{
  "version": 1,
  "source_id": "cam-scanner-2026-08-23",
  "torneo": { "nombre": "Clausura 2026" },
  "partidos": [
    {
      "nfecha": 3,
      "equipo": { "nombre": "MANDIYU SENIOR" },
      "rival": { "nombre": "CLUB SAN MIGUEL SENIOR" },
      "resultado": {
        "goles_equipo": 3
      },
      "jugadores": [
        {
          "documento": "24701343",
          "nombre": "ACUÑA IGNACIO OSCAR",
          "goles": 1,
          "amarillas": 0,
          "rojas": 0,
          "confianza": 0.98,
          "revisado": false
        }
      ]
    }
  ]
}
```

## Reglas de conciliación

- Torneo y equipos: por ID cuando se envía; si no, por nombre exacto normalizado por la BD.
- Fixture: por `idFixture` o por torneo + equipo/rival + opcionalmente `nfecha`.
- Jugador: por DNI y validación de pertenencia al equipo.
- Confianza menor a 0.85 genera advertencia y bloquea confirmación hasta marcar `revisado=true`.
- Si la suma de goles individuales no coincide con `goles_equipo`, se genera advertencia.
- Un partido visto desde cualquiera de sus dos planillas se resuelve contra el mismo `idFixture`.

## Idempotencia

Para cada jugador incluido en un partido confirmado:

- Los goles existentes de ese jugador/fixture se reemplazan por la cantidad informada.
- Las tarjetas simples del partido se reemplazan por las informadas.
- Las rojas que ya tengan motivo o hasta-fecha (sanción administrada) se preservan.

Por lo tanto, reenviar el mismo lote no vuelve a sumar goles ni tarjetas.


## Resultado por planilla individual

Las planillas físicas informan el resultado desde la perspectiva del equipo de esa hoja. Por eso `goles_equipo` es obligatorio y `goles_rival` es opcional.

Si llegan las dos planillas del mismo partido, el preview las consolida sobre el mismo `idFixture`:

- planilla del local -> completa `GolLocal`
- planilla del visitante -> completa `GolVisitante`
- si ambas informan además `goles_rival`, los valores deben coincidir
- si existe una contradicción entre las dos hojas, la importación queda bloqueada
- si al finalizar el lote falta uno de los dos lados del resultado, el preview queda incompleto y no puede confirmarse

Esto evita inventar el resultado del rival cuando la hoja escaneada no lo informa de forma explícita.
