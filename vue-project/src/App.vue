<template>
  <header>
    <div class="wrapper">
      <router-view></router-view>
      <EmployeeList />
    </div>
  </header>

  <main>
  </main>
</template>


<script>
import EmployeeList from './components/EmployeeList.vue';
export default {
  name: 'App',
  components: {
    EmployeeList
  }

}
</script>

