import { defineStore } from 'pinia'

const useUserStore = defineStore({
  id: 'user',
  state: () => {
    return {
      userInfo: {},
      token: '',
      isLogin: false,
      isBind: false,
      isBindMobile: false,
      isBindEmail: false,
      isBindWechat: false,
      isBindAlipay: false,
      isBindQQ: false,
      isBindWeibo: false,
      isBindGithub: false,
    }
  },
  actions: {
    getUserInfo() {
      return this.userInfo
    },
    getToken() {
      return this.token
    },
    setToken(data) {
      this.token = data
    },
    setUserInfo(data) {
      this.userInfo = data
    }
  }
})
export default useUserStore