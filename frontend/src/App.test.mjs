import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import test from 'node:test'

const source = readFileSync(new URL('./App.vue', import.meta.url), 'utf8')

test('stores routed view components outside deep Vue reactivity', () => {
  assert.match(source, /import\s+\{[^}]*markRaw[^}]*onBeforeUnmount[^}]*onMounted[^}]*shallowRef[^}]*\}\s+from\s+['"]vue['"]/)
  assert.match(source, /const\s+currentView\s*=\s*shallowRef\(/)
  assert.match(source, /currentView\.value\s*=/)
  assert.match(source, /markRaw\(/)
})
