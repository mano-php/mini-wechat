const baseUrl = "http://172.17.0.201:9810";
const TIMEOUT = 10000;
import { object } from "@climblee/uv-ui/libs/function/test";
import { local } from "./index";
async function request(options) {
  const token = local("token") || "";
  let header = Object.assign({}, options.header || {});
  if (!options.header || !options.header.token) {
    header.token = token;
  }
  return new Promise((resolve, reject) => {
    uni.request({
      url: baseUrl + options.url,
      method: options.method || "GET",
      data: options.data || {},
      header: header || {},
      timeout: options.timeout || TIMEOUT,
      success: (res) => {
        handleStatusCode(res.data);
        resolve(res.data);
      },
      fail: (err) => {
        console.log(err);
        reject(err);
      },
    });
  });
}
// 处理状态码提示
function handleStatusCode(response) {
  switch (response.status) {
    case 200:
      break;
    case 600:
      break;
    case 400:
      uni.showToast({ title: response.message || "错误的请求", icon: "none" });
      break;
    case 401:
      uni.showToast({
        title: response.message || "未授权，请重新登录",
        icon: "none",
      });
      break;
    case 403:
      uni.showToast({ title: response.message || "拒绝访问", icon: "none" });
      break;
    case 404:
      uni.showToast({
        title: response.message || "请求错误，未找到该资源",
        icon: "none",
      });
      break;
    case 500:
      uni.showToast({ title: response.message || "服务器错误", icon: "none" });
      break;
    default:
      uni.showToast({
        title: response.message || `网络请求失败，状态码：${response.status}`,
        icon: "none",
      });
      break;
  }
}

// GET请求封装
export function get(url, data, header) {
  return request({ url, method: "GET", data, header });
}
// POST请求封装
export function post(url, data, header) {
  return request({ url, method: "POST", data, header });
}
export function base_url() {
  return baseUrl
}
