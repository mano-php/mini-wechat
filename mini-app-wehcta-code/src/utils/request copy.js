import axios from "axios";
import axiosAdapterUniapp from 'axios-adapter-uniapp'
const baseUrl = "http://172.17.0.27:8199";
const TIMEOUT = 10000;
import { local, removeLocal } from "./index";
// 存储请求
let requests = [];
let bol = false;
let newToken = "";

let errorFn = (status, res = { code: 502 }) => {
  let msg = res.error;
  let map = {
    400: "请求报文语法错误或参数错误(400)",
    401: "需要通过HTTP认证，或认证失败(401)",
    403: "请求资源被拒绝(403)",
    404: "无法找到请求资源(404)",
    408: "请求超时(408)",
    422: "数据校验失败(422)",
    500: "服务器故障或Web应用故障(500)",
    501: "服务未实现(501)",
    502: "网络错误(502)",
    503: "服务器超负载或停机维护(503)",
    504: "网络超时(504)",
    505: "HTTP版本不受支持(505)",
  };
  if (status) {
    if (status === 401) {
      removeLocal('token');
    } else if (status === 403) {
      // vueRouter.replace('/no-permission');
    } else {
      // message.error(msg || map[status] || `连接出错(${status})!`);
    }
    return;
  } else {
    // message.error('请连接互联网！');
  }
};
axios.defaults.withCredentials = true;
// axios.post全局默认值 "application/x-www-form-urlencoded"
axios.defaults.headers.post["Content-Type"] = "application/json";

const request = axios.create({
  // 前后端同站点部署,相对路径,不同站点需要配置baseURL
  // baseURL: 'https://auth.uupt.work/',
  adapter: axiosAdapterUniapp,
  baseURL: baseUrl,
  timeout: TIMEOUT,
  
  // withCredentials: true
  // `transformRequest` 允许在向服务器发送前，修改请求数据
  // 只能用在 'PUT', 'POST' 和 'PATCH' 这几个请求方法
  // axios 默认把提交参数进行了JSON.stringify转换,这里强制改成表单方式  data: "mobile=18538300839&BizCode=13&type=2&validateType=1"
  // return qs.stringify(data);
  //   }
  // ],
});

request.interceptors.request.use(
  (config) => {
    console.log("config", config);
    let { url, method, params, data } = config;
    config.headers["Accept"] = "application/json";
    let token = local('token');
    if (token) {
      config.headers['token'] = token;
      config.headers['Token'] = token;
    }
    console.log("config.headers.Token", config.headers.Token)
    console.log("config.headers.token", config.headers.token)
    return config;
  },
  (err) => {
    return err;
  }
);

request.interceptors.response.use(
  (response) => {
    let { config, data } = response;
    // console.group(
    //     `${config.method?.toLocaleUpperCase()}接口: ${config.url}`
    // );
    console.groupEnd();
    // 业务层异常
    if (response.status !== 200) {
      config.headers.notoast || message.error(data.message);
      return Promise.resolve(data);
    }

    return data;
  },
  (err) => {
    console.log("err", err.message);
    if (err.response) {
      let { config, status } = err.response;
      let { error, code } = err.response.data;
      console.warn(401, err.response.data);
      // token失效, 刷新token
      if (status == "401") {
        if (code === 61002) {
          // 61002: token被加入黑名单，跳转登录
          // passport.logout();
        } else if (code === 61001) {
          // token过期，前端刷新token
          // if (!localStorage.getItem('token') || config.url.includes('/auth/refresh_token')) {
          //   passport.logout();
          //   return;
          // }
          // if (!bol) {
          //   bol = true;
          //   corp
          //     .refresh_token()
          //     .then(res => {
          //       let { token } = res;
          //       if (token) {
          //         newToken = token;
          //         localStorage.setItem('token', token);
          //         requests.forEach(cb => cb(newToken));
          //         requests = [];
          //       }
          //     })
          //     .catch(err => {
          //       // 刷新token接口失败
          //       vueRouter.push('/');
          //     })
          //     .finally(() => {
          //       bol = false;
          //     });
          // }
          // 正在刷新token，返回一个未执行resolve的promise
          return new Promise((resolve) => {
            // 将resolve放进队列，用一个函数形式来保存，等token刷新后直接执行
            requests.push((token) => {
              config.headers["Authorization"] = `Bearer ${token}`;
              resolve(request(config));
            });
          });
        } else {
          // 其余的code 跳转登录
          // passport.logout();
        }
      } else {
        errorFn(err.response.status, err.response.data);
        console.log("网络错误: ", err.response);
        return Promise.reject(err.response);
      }
    } else {
      // errorFn(err.response.status, err.response.data);
      // console.log("网络错误: ", err.response);
      return Promise.reject(err);
    }
  }
);

export default request;
