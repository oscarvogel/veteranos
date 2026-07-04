Veteranos - despliegue /nueva_web

1. Subir el contenido de este directorio a public_html/nueva_web.
2. Crear en el servidor el archivo api/.env copiando api/.env.example.
3. Completar DB_HOST, DB_NAME, DB_USER, DB_PASS y DB_CHARSET con las credenciales reales.
4. Mantener API_DEBUG=false en produccion.
5. Verificar:
   - https://veteranos.ar/nueva_web/api/health
   - https://veteranos.ar/nueva_web/api/health/db
   - https://veteranos.ar/nueva_web/

Notas:
- No subir credenciales dentro del ZIP local.
- El frontend de produccion consume https://veteranos.ar/nueva_web/api.
- La API Slim se publica en api/public/index.php y el .htaccess redirige /api hacia ese front controller.
