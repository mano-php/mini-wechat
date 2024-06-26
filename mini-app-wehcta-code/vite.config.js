import { defineConfig } from 'vite'
import uni from '@dcloudio/vite-plugin-uni'
import ENV_CONFIG from "./src/utils/envConfig";
// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    uni(),
  ],
  server: {
    port: 8000,
    strictPort: true,
    hmr: {
      port: 8000,
      host: 'localhost'
    }
  },
  define: {
    "process.env.config": ENV_CONFIG, //配置一
    'process.env': process.env, //配置二
  },
})
