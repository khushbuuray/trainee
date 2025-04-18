import { createRouter, createWebHistory } from "vue-router";
import AddOrEdit from "./components/AddOrEdit.vue";

const routes = [{
  path:'/',
  component:AddOrEdit
}];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
