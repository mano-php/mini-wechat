<template>
  <view class="container">
    <uv-navbar title="个人资料" :fixed="false" @leftClick="backTo"></uv-navbar>
    <view class="flex-1 content">
      <view class="content-box">
        <uv-cell-group class="content-box">
          <uv-cell title="头像" :center="true" isLink>
            <template v-slot:value>
              <uv-avatar :src="userInfo.avatar" @tap="changeAvatar"></uv-avatar>
            </template>
          </uv-cell>
          <uv-cell
            title="用户名"
            :value="userInfo.nickname"
            :center="true"
            isLink
            @tap="showChangeNickname"
          ></uv-cell>
          <uv-cell title="性别" :center="true" isLink @tap="openChangeSex">
            <template v-slot:value>
              <text class="cell-text" v-if="userInfo.sex == 1">男</text>
              <text class="cell-text" v-else>女</text>
            </template>
          </uv-cell>
          <uv-cell title="我的生日" :center="true" isLink @tap="openChangeDate">
            <template v-slot:value>
              <text class="cell-text" v-if="userInfo.luck_date">{{
                userInfo.luck_date
              }}</text>
              <text class="cell-text" v-else>完善信息</text>
            </template>
          </uv-cell>
          <uv-cell
            title="关联手机"
            :value="hidePhoneNumber(userInfo.mobile)"
            :center="true"
          ></uv-cell>
          <uv-cell title="收货地址" :center="true" isLink @click="goTo('/pages/address/index')"></uv-cell>
        </uv-cell-group>
      </view>
    </view>
    <view style="padding: 0 24rpx">
      <uv-button
        class="btn"
        type="primary"
        shape="circle"
        text="退出登录"
        @tap="logOut"
      ></uv-button>
    </view>
    <uv-safe-bottom></uv-safe-bottom>
    <uv-popup ref="modal" mode="bottom" :closeOnClickOverlay="false">
      <view class="model">
        <view class="model-title">获取头像</view>
        <view class="model-content">请允许获取您的头像信息</view>
      </view>
      <uv-button text="同意授权" @tap="getWxInfo"> </uv-button>
    </uv-popup>
    <uv-modal ref="edit" title="修改用户名">
      <view class="slot-content">
        <uv-form
          ref="form"
          labelPosition="left"
          :model="formData"
          :rules="rules"
          labelWidth="60"
        >
          <uv-form-item label="用户名" prop="name" borderBottom>
            <uv-input v-model="formData.name" border="none"> </uv-input>
          </uv-form-item>
        </uv-form>
      </view>
      <template v-slot:confirmButton>
        <view class="btnBar">
          <view class="flex-1 btnDefault" @tap="closeChangeNickName">取消</view>
          <view class="flex-1 btnConfirm" @tap="confirmChangeNickName">确定</view>
        </view>
      </template>
    </uv-modal>
    <uv-action-sheet
      ref="actionSheet"
      :actions="sexList"
      @select="selectSex"
      safeAreaInsetBottom
      cancelText="取消"
      :closeOnClickAction="false"
    >
    </uv-action-sheet>
    <uv-datetime-picker ref="datetimePicker" v-model="dateData" mode="date" @confirm="changeDate"></uv-datetime-picker>
  </view>
</template>

<script setup>
import { ref, computed } from "vue";
import { backTo, local, goTo, removeLocal } from "@/utils/index";
import { getUserInfo, setUserInfo, postPay } from "@/api/login";
import useUserStore from "@/store/modules/user";
if (!local('token')) {
  uni.showToast({title:'暂未登录，请前去登录',icon:'none'})
  setTimeout(() => {
    goTo('/pages/login/login', 'redirectTo');
  }, 1500)
  
}
const userStore = useUserStore();
// const userInfo = computed(() => userStore.userInfo);
const base_url = "http://172.17.0.27:8199";
// 个人数据
const userInfo = ref({
  avatar: "",
  nickname: "",
  sex: 1,
  luck_date: "",
  mobile: "",
  address: "",
});
// 表单验证规则
const rules = ref({
  name: {
    required: true,
    message: "请输入您的用户名",
    trigger: "blur",
  },
});
// 表单实例
const form = ref();
// 弹窗实例
const edit = ref();
// 表单数据
const formData = ref({
  name: "",
});
// 修改名称弹窗
function showChangeNickname() {
  formData.value.name = userInfo.value.nickname;
  edit.value.open();
}
// 确认修改用户昵称
function confirmChangeNickName() {
  form.value.validate().then(() => {
    if (formData.value.name == userInfo.value.nickname) {
      edit.value.close();
      return;
    }
    uni.showLoading()
    console.log("userInfo.value", userInfo.value);
    setUserInfo({
      nickname: formData.value.name,
    })
      .then((res) => {
        console.log("res", res);
        if (res.status == 200) {
          userInfo.value.nickname = formData.value.name;
          userStore.setUserInfo(userInfo.value);
          uni.hideLoading();
          edit.value.close();
        }
      })
      .catch((err) => {
        console.log("err", err)
      })
      .finally(() => {
        uni.hideLoading();
      });
  });
}
// 关闭修改用户昵称弹窗
function closeChangeNickName() {
  edit.value.close();
}
// 性别选项
const sexList = ref([
  {
    name: "男",
    value: 1,
  },
  {
    name: "女",
    value: 2,
  },
]);
// 性别下拉实例
const actionSheet = ref();
// 打开修改性别sheet
function openChangeSex() {
  actionSheet.value.open();
}
// 选择性别
function selectSex(e) {
  console.log("e", e);
  if (+e.value == userInfo.value.sex) {
    actionSheet.value.close();
    return;
  }
  uni.showLoading()
  setUserInfo({
    sex: e.value,
  })
    .then((res) => {
      console.log("res", res);
      if (res.status == 200) {
        userInfo.value.sex = e.value;
        userStore.setUserInfo(userInfo.value);
        uni.hideLoading();
        actionSheet.value.close();
      }
    })
    .finally(() => {
      uni.hideLoading();
      actionSheet.value.close();
    });
}
// 日期选择实例
const datetimePicker = ref()
// 日期数据
const dateData = ref()
// 打开修改生日
function openChangeDate() {
  dateData.value = Number(new Date())
  if (userInfo.value.luck_date) {
    dateData.value = Number(new Date(userInfo.value.luck_date))
  }
  datetimePicker.value.open()
}
// 修改生日
function changeDate(e) {
  let date = uni.$uv.timeFormat(e.value)
  if (date == userInfo.value.luck_date) {
    datetimePicker.value.close()
    return
  }
  uni.showLoading()
  setUserInfo({
    luck_date: date,
  })
    .then((res) => {
      console.log("res", res);
      if (res.status == 200) {
        userInfo.value.luck_date = date;
        userStore.setUserInfo(userInfo.value);
        uni.hideLoading();
        datetimePicker.value.close();
      }
    })
    .finally(() => {
      uni.hideLoading();
      datetimePicker.value.close();
    });
}
// 格式化手机号
function hidePhoneNumber(phoneNumber) {
  if (phoneNumber.length !== 11) {
    return '手机号格式不正确';
  }
  
  var hiddenDigits = '****';
  var start = phoneNumber.slice(0, 3);
  var end = phoneNumber.slice(7);
  
  return start + hiddenDigits + end;
}


function logOut() {
  // modal.value.open();
  // postPay().then(res => {
  //   console.log("res", res)
  // })
  removeLocal('token')
  goTo('/pages/login/login', 'redirectTo');
}
const modal = ref();
if (!userInfo.value || !userInfo.value.avatarUrl) {
  getInfo();
}
// 获取用户信息
function getInfo() {
  console.log("local('token')", local('token'))
  getUserInfo()
    .then((res) => {
      console.log("res", res.data);
      userInfo.value = res.data;
      userStore.setUserInfo(res.data);
      // if (res.data.avatar) {
        
      // } else {
      //   // modal.value.open();
      // }
    })
    .finally(() => {
      uni.hideLoading();
    });
}
// 获取微信头像等，暂时弃用
function getWxInfo() {
  uni.showLoading({
    title: "加载中",
    mask: true,
  });
  uni.getUserProfile({
    desc: "测试获取用户信息",
    success: (info) => {
      setUserInfo({
        avatar: info.userInfo.avatarUrl,
        nickname: info.userInfo.nickName,
      })
        .then((res) => {
          userStore.setUserInfo(res.data);
          uni.hideLoading();
          modal.value.close();
        })
        .finally(() => {
          uni.hideLoading();
        });
    },
    fail: (err) => {
      console.log("err", err);
    },
  });
}
function changeAvatar() {
  uni.chooseImage({
    count: 1, //默认9
    extension: [".jpg", ".png", ".jpeg", ".gif"],
    success: (chooseImageRes) => {
      console.log("chooseImageRes", chooseImageRes);
      const size = chooseImageRes.tempFiles[0].size;
      if (size >= 1024 * 1024 * 5) {
        uni.showToast({title:'所选图片大小不能超过5M',icon:'none'})
        return;
      }
      uni.showLoading({ title: "上传中...", mask: true });
      const tempFilePaths = chooseImageRes.tempFilePaths;
      uni.uploadFile({
        url: `${base_url}/api/mini-app/upload`,
        filePath: tempFilePaths[0],
        name: "image",
        success: (res) => {
          console.log("res", res);
          if (res.statusCode == 200) {
            const response = JSON.parse(res.data);
            console.log("response", response)
            if (response.status == 200) {
              setUserInfo({
                avatar: userInfo.value.avatar,
              })
                .then((res) => {
                  userInfo.value.avatar = response.data.path;
                  userStore.setUserInfo( userInfo.value);
                  uni.hideLoading();
                  modal.value.close();
                })
                .finally(() => {
                  uni.hideLoading();
                });
            } else {
              uni.hideLoading();
              uni.showToast({title:'上传失败，请检查您的网络',icon:'none'})
            }
          } else {
            uni.showToast({title:'上传失败，请检查您的网络',icon:'none'})
          }
        },
        fail: (error) => {
          console.log("error", error);
          uni.hideLoading();
        },
      });
    },
  });
}
</script>
<style scoped lang="scss">
.cell-text {
  text-align: right;
  font-size: 14px;
  line-height: 24px;
  color: #606266;
}
.btn {
  padding: 32upx;
}
.content {
  padding: 24upx;
  &-box {
    background-color: #ffffff;
    border-radius: 10upx;
  }
}
.btnBar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  text-align: center;
  border-top: 1px solid rgb(214, 215, 217);
  .flex-1 {
    padding: 24rpx;
  }
  .btnDefault {
    border-right: 1px solid rgb(214, 215, 217);
  }
  .btnConfirm {
    color: $uni-color-primary;
  }
}
</style>
