// 导出的环境请求地址
//本地环境
const dev = {
  ENV: "dev",
  VITE_BASE_API: "http://127.0.0.1:8080/dev-api",
};
//正式环境
const pro = {
  ENV: "pro",
  VITE_BASE_API: "https://xxx/prod-api",
};
//测试环境
const test = {
  ENV: "test",
  VITE_BASE_API: "https://xxx/test-api",
};
export default {
  dev,
  test,
  pro,
};