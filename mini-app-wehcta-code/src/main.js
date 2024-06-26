import {
	createSSRApp
} from "vue";
import uvUI from '@climblee/uv-ui'
import store from '@/store'
import './uni.scss'
// #ifndef VUE3
Vue.use(uvUI);
// #endif
import App from "./App.vue";
export function createApp() {
	const app = createSSRApp(App);
	app.use(store)
	return {
		app,
	};
}
