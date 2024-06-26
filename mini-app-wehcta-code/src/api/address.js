import {get, post} from '@/utils/request'

/**
 * 获取地址列表
 * @returns {Promise} 返回一个Promise对象，成功时携带登录结果，失败时携带错误信息
 */
export function getAddress() {
  // 使用post方法向'/api/mini-app/code-login'发送登录请求
  return get('/api/mini-app/address/list')
}

/**
 * 添加地址
 * @param {Object} data 包含登录所需信息的对象
 * @returns {Promise} 返回一个Promise对象，成功时携带登录结果，失败时携带错误信息
 */
export function addAddress(data) {
  // 使用post方法向'/api/mini-app/code-login'发送登录请求
  return post('/api/mini-app/address/create', data)
}
/**
 * 修改地址
 * @param {Object} data 包含登录所需信息的对象
 * @returns {Promise} 返回一个Promise对象，成功时携带登录结果，失败时携带错误信息
 */
export function editAddress(data) {
  // 使用post方法向'/api/mini-app/code-login'发送登录请求
  return post('/api/mini-app/address/save', data)
}
/**
 * 删除地址
 * @param {Object} data 包含登录所需信息的对象
 * @returns {Promise} 返回一个Promise对象，成功时携带登录结果，失败时携带错误信息
 */
export function deleteAddress(data) {
  // 使用post方法向'/api/mini-app/code-login'发送登录请求
  return post('/api/mini-app/address/delete', data)
}
/**
 * 设为默认
 * @param {Object} data 包含登录所需信息的对象
 * @returns {Promise} 返回一个Promise对象，成功时携带登录结果，失败时携带错误信息
 */
export function defaultAddress(data) {
  // 使用post方法向'/api/mini-app/code-login'发送登录请求
  return post('/api/mini-app/address/default', data)
}