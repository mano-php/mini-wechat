import {post,get} from '../utils/request'

/**
 * 通过code进行登录
 * @param {Object} data 包含登录所需信息的对象
 * @returns {Promise} 返回一个Promise对象，成功时携带登录结果，失败时携带错误信息
 */
export function codeLogin(data) {
  // 使用post方法向'/api/mini-app/code-login'发送登录请求
  return post('/api/mini-app/code-login', data)
}

/**
 * 获取配置接口
 * @returns {*}
 */
export function getAppConfig() {
  // 获取配置
  return get('/api/mini-app/get-config', )
}


/**
 * 绑定手机号
 * @param {Object} data - 包含绑定手机号所需数据的对象
 * @returns {Promise} 返回一个Promise对象，成功时返回绑定结果，失败时返回错误信息
 */
export function bindMobile(data) {
  // 发送登录请求
  return post('/api/mini-app/bind-mobile', data)
}
/**
 * 设置用户信息
 * @param {Object} data - 包含用户信息的数据对象
 * @returns {Promise} - 返回一个Promise对象，用于处理登录请求的结果
 */
export function setUserInfo(data) {
  // 发送登录请求到'/api/mini-app/save-info'接口
  return post('/api/mini-app/save-info', data)
}
/**
 * 获取用户信息
 * @param {Object} data - 登录请求所需的数据
 * @returns {Promise} - 返回一个Promise对象，成功时携带用户信息
 */
export function getUserInfo(data) {
  // 发送登录请求并返回Promise对象
  return post('/api/mini-app/get-info', data)
}
/**
 * 执行支付操作
 * 本函数用于发送支付请求，具体为向'/api/mini-app/payment/mini-wechat-put'路径发起POST请求。
 * 
 * @returns {Promise} 返回一个Promise对象，用于处理支付请求的结果。
 */
export function postPay() {
  // 发送登录请求并返回Promise对象
  return post('/api/mini-app/payment/mini-wechat-put')
}