<template>
  <div class="fixed-center text-center">
    <p>
      <img
        src="~assets/sad.svg"
        style="width:30vw;max-width:150px;"
      >
    </p>
    <p class="text-faded">
      Desculpe, página não encontrada...<strong>(404)</strong>
    </p>
    <q-btn
      color="secondary"
      style="width:200px;"
      @click="$router.push('/')"
      label="Voltar"
    />
  </div>
</template>

<script>
import {
  QBtn
} from 'quasar'

export default {
  name: 'Error404',
  components: { QBtn }
}
</script>
