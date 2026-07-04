import assert from 'node:assert/strict'
import test from 'node:test'

import {
  getApiErrorMessage,
  getResponseErrorMessage,
  isModernApiSuccess,
} from './api-response.js'

test('detects successful modern API payloads', () => {
  assert.equal(isModernApiSuccess({ success: true, data: [] }), true)
  assert.equal(isModernApiSuccess({ success: false, error: { message: 'Fallo' } }), false)
  assert.equal(isModernApiSuccess(null), false)
})

test('extracts error message from modern API response payload', () => {
  assert.equal(
    getResponseErrorMessage({ success: false, error: { message: 'No se pudo completar la consulta.' } }),
    'No se pudo completar la consulta.',
  )
})

test('falls back when modern API response has no error message', () => {
  assert.equal(getResponseErrorMessage({ success: false }), 'No se pudo cargar la informacion.')
})

test('extracts thrown axios error message from response data first', () => {
  const error = {
    message: 'Request failed',
    response: { data: { success: false, error: { message: 'Parametro torneo_id requerido' } } },
  }

  assert.equal(getApiErrorMessage(error), 'Parametro torneo_id requerido')
})
