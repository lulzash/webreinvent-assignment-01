import './bootstrap';

import { createApp } from 'vue';
import ToDoApp from './components/ToDoApp.vue';

const app = createApp({});
app.component('todo-app', ToDoApp);
app.mount('#app');
