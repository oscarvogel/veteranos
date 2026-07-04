<template>
  <div class="min-h-screen flex flex-col bg-gray-50/50">
    <NavBar />
    <main class="flex-1 container mx-auto p-4">
      <component :is="currentView" />
    </main>
    <FooterComp />
  </div>
</template>

<script>
import { markRaw, onBeforeUnmount, onMounted, shallowRef } from 'vue'
import NavBar from './components/NavBar.vue'
import FooterComp from './components/Footer.vue'
import Posiciones from './views/Posiciones.vue'
import Goleadores from './views/Goleadores.vue'
import Resultados from './views/Resultados.vue'
import Tarjetas from './views/Tarjetas.vue'
import ListaBuenaFe from './views/ListaBuenaFe.vue'

const viewMap = {
  '/': markRaw(Posiciones),
  '/fixture': markRaw(Posiciones),
  '/resultados': markRaw(Resultados),
  '/tarjetas': markRaw(Tarjetas),
  '/goleadores': markRaw(Goleadores),
  '/lista': markRaw(ListaBuenaFe),
}

export default {
  components: { NavBar, FooterComp },
  setup() {
    const currentView = shallowRef(viewMap['/fixture'])
    const updateView = () => {
      const hash = (window.location.hash || '#/fixture').replace(/^#/, '')
      currentView.value = viewMap[hash] || viewMap['/fixture']
    }

    onMounted(() => {
      updateView()
      window.addEventListener('hashchange', updateView)
    })

    onBeforeUnmount(() => {
      window.removeEventListener('hashchange', updateView)
    })

    return { currentView }
  },
}
</script>
