<template>
  <view class="container">
    <uv-navbar title="登录" :fixed="false" leftIcon=""></uv-navbar>

    <view class="flex-1">
      <view v-if="loginType != 2" class="image-logo">
        <uv-image :src="config.app_logo" mode="widthFix" width="80rpx"></uv-image
        >{{config.app_name}}
      </view>
      <view class="image-area">
        <uv-image :src="config.banner_img" mode="widthFix" width="375rpx"></uv-image>
      </view>
      <view class="form-area">
        <uv-form
          v-if="loginType == 2"
          labelPosition="left"
          :model="formData"
          :rules="rules"
          ref="form"
          class="form-area"
        >
          <uv-form-item prop="phone" borderBottom>
            <uv-input
              v-model="formData.phone"
              border="none"
              placeholder="请输入手机号"
            >
            </uv-input>
          </uv-form-item>
          <uv-form-item prop="code" borderBottom>
            <uv-input
              v-model="formData.code"
              placeholder="请输入验证码"
              border="none"
            >
            </uv-input>
            <template v-slot:right>
              <uv-code
                :seconds="seconds"
                @end="end"
                @start="start"
                ref="uCodeRef"
                @change="codeChange"
              ></uv-code>
              <uv-button @tap="getCode" shape="circle">{{ tips }}</uv-button>
            </template>
          </uv-form-item>
        </uv-form>
      </view>

      <view class="btn-area">
        <view class="mb-15">
          <uv-button
            type="error"
            shape="circle"
            :text="loginType == 2 ? '立即登录' : '快速登录'"
            @tap="goLogin"
          >
          </uv-button>
        </view>
        <view class="mb-15">
          <uv-button type="info" shape="circle" text="暂不登录"> </uv-button>
        </view>
      </view>
    </view>

    <view class="msg-area">
      <view class="msg-btn" @tap="changeLoginType">
        <template v-if="loginType == 2">
          <text>快速登录</text>
        </template>
      </view>
      <view class="msg-info">{{config.tip_msg}}</view>
    </view>
    <uv-popup ref="modal" mode="bottom" :closeOnClickOverlay="false">
      <view class="model">
        <view class="model-title">用户隐私保护提示</view>
        <view class="model-content"
          >感谢您使用{{config.app_name}}}，在您使用UU智能工单前应当阅读并同意
          <uv-text
            mode="link"
            :text="`《${config.app_name}隐私保护指引》`"
            :href="config.protocol"
            class="asd"
          ></uv-text
          >，当您点击同意并开始使用产品服务时，即表示您已理解并同意该条款内容，该条款将对您产生法律效力</view
        >
        <view @tap="showLoading" style="padding: 0 24rpx">
          <uv-button
            text="同意并继续"
            type="primary"
            open-type="getPhoneNumber"
            @getphonenumber="getPhoneNumber"
          >
          </uv-button>
        </view>
      </view>
    </uv-popup>
  </view>
</template>

<script setup>
import { goTo, local } from "@/utils/index";
import { ref } from "vue";
import { getAppConfig,codeLogin, bindMobile } from "@/api/login";
function confirm() {}
const loginType = ref(1);
const formData = ref({
  phone: "",
  code: "",
});
const config = ref({
  app_name:'',
  app_logo:'',
  banner_img:'',
  tip_msg:'',
  protocol:''
})
/**
 * 获取页面配置
 */
getAppConfig().then(res=>{
  config.value = res.data
})

const modal = ref();
const rules = ref({
  phone: {
    type: "string",
    required: true,
    message: "请填写手机号",
    trigger: ["blur", "change"],
  },
  code: {
    type: "string",
    required: true,
    message: "请填写验证码",
    trigger: ["blur", "change"],
  },
});

function showLoading() {
  uni.showLoading({ title: "", mask: true });
}
function getPhoneNumber(e) {
  console.log("e", e);
  modal.value.close();
  if (e.code) {
    uni.hideLoading();
    uni.showLoading({
      title: "正在绑定中",
      mask: true,
    });
    bindMobile({ bind_key: bindData.value.bind_key, code: e.code })
      .then((res) => {
        console.log("res", res);
        if (res.status == 200) {
          local("token", res.data.token);
          goTo("/pages/setting/index");
        }
      })
      .finally(() => {
        uni.hideLoading();
      });
  } else {
    uni.hideLoading();
    uni.showToast({title:'请授权获取手机号',icon:'none'})
  }
}
const tips = ref("获取验证码");
const seconds = ref(60);
const fitst = ref(true);
const uCodeRef = ref(null);

const codeChange = (text) => {
  tips.value = text;
};
const bindData = ref();
async function goLogin() {
  if (loginType.value != 2) {
    // #ifdef H5
    uni.showToast({title:'请在微信环境使用',icon:'none'})
    // #endif
    // #ifdef MP-WEIXIN
    // uni.navigateTo({
    //   url: "/pages/login/login",
    // });
    await uni.login({
      provider: "weixin", //使用微信登录
      success: async (res) => {
        console.log("res", res);
        uni.showLoading({
          title: "正在登录中",
          mask: true,
        });
        await codeLogin({ code: res.code })
          .then((rec) => {
            console.log("rec", rec);
            // 200 是成功、400 是错误、900 是需要登录、600 是需要绑定手机号
            if (rec.status == 600) {
              bindData.value = rec.data;
              modal.value.open();
            }
            if (rec.status == 200) {
              local("token", rec.data.token);
              goTo("/pages/setting/index");
            }
          })
          .finally(() => {
            uni.hideLoading();
          });
      },
      fail: (err) => {
        console.log("err", err);
      },
    });
    // #endif
  } else {
    // 账号密码登录
    loginFn();
  }
}
// 账号密码登录
function loginFn() {
  uni.getUserProfile({
    desc: "测试获取用户信息",
    success: (info) => {
      console.log("info", info);
    },
    fail: (err) => {
      console.log("err", err);
    },
  });
}
function getCode() {
  console.log("uCodeRef.value", uCodeRef.value);
  console.log("uCodeRef.canGetCode", uCodeRef.value.canGetCode);
  if (uCodeRef.value.canGetCode) {
    // 模拟向后端请求验证码
    uni.showLoading({
      title: "正在获取验证码",
      mask: true,
    });
    setTimeout(() => {
      uni.hideLoading();
      // 这里此提示会被start()方法中的提示覆盖
      uni.showToast({title:'验证码已发送',icon:'none'})
      // 通知验证码组件内部开始倒计时
      uCodeRef.value.start();
    }, 2000);
  } else {
    uni.showToast({title:'倒计时结束后再发送',icon:'none'})
  }
}
function changeLoginType() {
  loginType.value = loginType.value === 2 ? 1 : 2;
  // console.log("process.env.ENV", process)
  const systemInfo = uni.getSystemInfoSync();
}
const end = () => {
  console.log("倒计时结束");
};

const start = () => {
  console.log("倒计时开始");
};
</script>
<style lang="scss" scoped>
.container {
  background-color: #ffffff;
  .image-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30rpx 0 10rpx;
  }
  .image-area {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 50rpx 0;
  }
  .btn-area {
    padding: 0 30rpx;
  }
  .msg-area {
    display: flex;
    flex-direction: column;
    width: 100%;
    padding-bottom: 30rpx;
    .msg-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 15rpx;
      color: $uni-text-color;
      .uv-icon {
        margin-right: 5rpx;
      }
    }
    .msg-info {
      width: 100%;
      font-size: 24rpx;
      color: $uni-text-color-grey;
      text-align: center;
    }
  }
  .mb-15 {
    margin-bottom: 15rpx;
  }
  .model {
    &-title {
      font-size: 32rpx;
      color: $uni-color-title;
      // margin-bottom: 24rpx;
      margin: 24rpx 0;
      text-align: center;
    }
    &-content {
      margin: 24rpx 0;
      font-size: 14px;
      padding: 0 24rpx;
      :deep(.uv-text) {
        display: inline !important;
        width: fit-content !important;
      }
      :deep(.uv-link) {
        display: inline !important;
        width: fit-content !important;
      }
    }
  }
}
.form-area {
  padding: 0 48rpx;
  margin-bottom: 48rpx;
}
:deep(.uv-form-item__body__right__message) {
  margin-left: 0 !important;
}
</style>
