import axios from 'axios'
export { getApiErrorMessage, getResponseErrorMessage, isModernApiSuccess } from './api-response.js'

const base = (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8017').replace(/\/$/, '')

const instance = axios.create({
  baseURL: base + '/api',
  headers: { 'Content-Type': 'application/json' },
  validateStatus: status => status >= 200 && status < 600,
})

export default instance
