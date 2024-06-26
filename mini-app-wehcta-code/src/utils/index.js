/**
 * 导航到指定的URL页面。
 * @param {string} url 需要导航到的页面的URL路径。
 * @param {string} [type=navigateTo] 导航类型，默认为'navigateTo'。可选的导航类型取决于支持的API。
 * @returns {void} 不返回任何内容。
 */
export function goTo(url, type = "navigateTo") {
  uni[type]({
    url,
  });
}

/**
 * 返回到前一个页面。
 * @param {number} delta - 返回的页面数，默认为1。表示返回前一页。
 */
export function backTo(delta = 1) {
  // 使用uni.navigateBack方法返回指定页数
  uni.navigateBack({
    delta,
  });
}
/**
 * 存储localsessoin
 * @param key
 * @param value
 * @returns
 */
export const local = (key, value) => {
  if (!key) {
    return console.warn("key must be a string");
  }
  if (value) {
    value = typeof value == "object" ? JSON.stringify(value) : value;
    uni.setStorageSync(key, value);
  } else {
    let data = uni.getStorageSync(key);
    try {
      data = JSON.parse(data);
    } catch (error) {}
    return data;
  }
};
/** 删除localsessoin */
export const removeLocal = (key) => {
  uni.removeStorageSync({
    key: key,
  });
};
/** 复制文本 */
export function copyToClipboard(content) {
  return new Promise((resolve, reject) => {
    if (typeof navigator === "undefined" || !navigator.clipboard) {
      return reject();
    }

    try {
      navigator.clipboard.writeText(content);
    } catch (error) {
      return reject();
    }
    return resolve("success");
  });
}
