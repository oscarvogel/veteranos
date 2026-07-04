const DEFAULT_ERROR = 'No se pudo cargar la informacion.'

export function isModernApiSuccess(payload) {
  return Boolean(payload && payload.success === true)
}

export function getResponseErrorMessage(payload, fallback = DEFAULT_ERROR) {
  return payload?.error?.message || fallback
}

export function getApiErrorMessage(error, fallback = DEFAULT_ERROR) {
  return getResponseErrorMessage(error?.response?.data, error?.message || fallback)
}
