import assert from 'node:assert/strict'
import test from 'node:test'

import config from '../vite.config.js'

test('uses polling watcher for reliable dev server on mapped Windows drives', () => {
  assert.equal(config.server?.watch?.usePolling, true)
  assert.equal(config.server?.host, '127.0.0.1')
  assert.equal(config.server?.port, 5173)
  assert.equal(config.server?.proxy, undefined)
})
