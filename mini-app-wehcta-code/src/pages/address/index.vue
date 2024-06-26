<template>
  <view class="container">
    <uv-navbar title="地址管理" :fixed="false" @leftClick="backTo"></uv-navbar>
    <scroll-view class="flex-1" scroll-y>
      <view class="address">
        <view v-for="item in list" class="address-item" :key="item.id">
          <view class="close" @tap="deleteThis(item)">
            <uv-icon name="close"></uv-icon>
          </view>
          <view class="flex align-center small">
            <view class="address-name">{{ item.name }}</view>
            <text>{{ item.mobile }}</text>
          </view>
          <view class="address-detail">
            {{ item.city.province || "" }}
            {{ item.city.city || "" }}
            {{ item.city.district || "" }}
            {{ item.address || "" }}
          </view>
          <view class="flex align-center justify-between">
            <view @tap="changeDefault(item)" class="flex align-center">
              <uv-switch
                v-model="item.default"
                asyncChange
                size="16"
              ></uv-switch>
              <text class="text" :class="{ act: item.default }">{{
                item.default ? "已设为默认" : "设为默认"
              }}</text>
            </view>
            <view @tap="openModal(2, item)">
              <text>编辑</text>
              <!-- <view>置顶</view> -->
            </view>
          </view>
        </view>
      </view>
    </scroll-view>
    <uv-button style="margin: 24rpx" @tap="openModal(1)">添加地址</uv-button>
    <uv-safe-bottom></uv-safe-bottom>

    <uv-modal ref="modal" :title="type == 'add' ? '新增地址' : '编辑地址'">
      <view class="slot-content">
        <uv-form
          ref="form"
          labelPosition="left"
          :model="formData"
          :rules="rules"
          labelWidth="120"
        >
          <uv-form-item label="收货人名称" prop="name" borderBottom>
            <uv-input
              v-model="formData.name"
              border="none"
              placeholder="请输入收货人名称"
            >
            </uv-input>
          </uv-form-item>
          <uv-form-item label="收货人电话" prop="mobile" borderBottom>
            <uv-input
              v-model="formData.mobile"
              border="none"
              placeholder="请输入收货人电话"
              type="number"
            >
            </uv-input>
          </uv-form-item>
          <uv-form-item label="所在地区" prop="city.city" borderBottom>
            <view @tap="openAddress">
              <template v-if="formData.city && formData.city.province && formData.city.city">
                {{ formData.city.province || "" }}
                {{ formData.city.city || "" }}
                {{ formData.city.district || "" }}
              </template>
              <uv-input
                v-else
                v-model="formData.city.province"
                border="none"
                placeholder="请选择地区"
              >
              </uv-input>
            </view>
            <uv-picker
              ref="picker"
              :columns="curList"
              keyName="name"
              @change="changeAddress"
              @confirm="confirmAddress"
            >
            </uv-picker>
          </uv-form-item>

          <uv-form-item label="详细地址" prop="address" borderBottom>
            <uv-input v-model="formData.address" border="none"> </uv-input>
          </uv-form-item>
        </uv-form>
      </view>
      <template v-slot:confirmButton>
        <view class="btnBar">
          <view class="flex-1 btnDefault" @tap="closeModel">取消</view>
          <view class="flex-1 btnConfirm" @tap="submitAddress">确定</view>
        </view>
      </template>
    </uv-modal>


    <uv-modal ref="deleteModel" title="确定删除此地址么？" content='删除后无法恢复哦，请谨慎操作' :asyncClose="true" @confirm="confirmDelete" showCancelButton cancelText="删除" confirmColor="#f56c6c"></uv-modal>
  </view>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { backTo, local, goTo } from "@/utils/index";
import {
  getAddress,
  addAddress,
  editAddress,
  deleteAddress,
  defaultAddress,
} from "@/api/address";
const list = ref([
  // {
  //   name: "",
  //   mobile: "",
  //   city: {
  //     province: '',
  //     city: '',
  //     district: '',
  //     street: '',
  //   },
  //   is_default: true,
  // },
]);
if (!local('token')) {
  uni.showToast({title:'暂未登录，请前去登录',icon:'none'})
  setTimeout(() => {
    goTo('/pages/login/login', 'redirectTo');
  }, 1500)
  
}
const formData = ref({
  name: "",
  mobile: "",
  city: {
    province: "",
    city: "",
    district: "",
    street: "",
  },
  address: ''
  // default: true,
});
const rules = ref({
  name: [
    { required: true, message: "请输入收货人名称", trigger: "blur" },
    { min: 1, max: 8, message: "长度在 1 到 8 个字符", trigger: "blur" },
  ],
  mobile: [
    { required: true, message: "请输入手机号", trigger: "blur" },
    {
      min: 11,
      max: 11,
      // pattern: /^1[3|4|5|7|8][0-9]\d{8}$/,
      message: "手机号格式不正确",
      trigger: "blur",
    },
    // { validator: validateMobile, trigger: 'blur' }
  ],
  "city.street": [
    { required: true, message: "请输入详细地址", trigger: "blur" },
  ],
  "city.city": [{ required: true, message: "请选择地区" }],
});
const modal = ref();
const type = ref("add");
function closeModel() {
  modal.value.close();
  formData.value = {
    name: "",
    mobile: "",
    city: {
      province: "",
      city: "",
      district: "",
      street: "",
    },
    address: ''
  }
}
function openModal(tp, data = {}) {
  if (tp == 2) {
    type.value = "edit";
    formData.value = data;
  } else {
    type.value = "add";
    formData.value = {
      name: "",
      mobile: "",
      city: {
        province: "",
        city: "",
        district: "",
        street: "",
      },
      address: ''
    }
  }
  modal.value.open();
}
const form = ref();
async function submitAddress() {
  console.log("formData.value", formData.value);
  await form.value.validate().then(async (res) => {
    console.log("res", res);
    uni.showLoading({
      title: "加载中",
      mask: true,
    });
    try {
      if (type.value == "add") {
        await addAddress(formData.value);
      } else {
        await editAddress(formData.value);
      }
      getMyAddress()
    } catch (error) {
      console.log("error", error);
    }
    uni.hideLoading();
    closeModel();
  });
}

function changeDefault(item) {
  // console.log("e", e)
  console.log("item.is_default", item.is_default);
  uni.showLoading({
    title: "加载中",
    mask: true,
  });
  defaultAddress({ id: item.id })
    .then((res) => {
      getMyAddress()
    })
    .finally(() => {
      uni.hideLoading()
    });
}

const deleteModel = ref()
function deleteThis(item) {
  console.log("item", item);
  deleteModel.value.open()
  formData.value = item
}
function confirmDelete() {
  uni.showLoading({
    title: '删除中',
    mask: true
  })
  deleteAddress({ id: formData.value.id }).then(res => {
    console.log("res", res)
    getMyAddress()
  }).finally(() => {
    uni.hideLoading()
    deleteModel.value.close()
  })
}

const picker = ref();
function openAddress() {
  picker.value.open();
}
const curList = ref();
const addressList = ref();
const provinces = ref();
const citys = ref();
const districts = ref();
uni.request({
  url: "https://webapi.amap.com/ui/1.1/ui/geo/DistrictExplorer/assets/d_v1/country_tree.json",
  method: "GET",
  success: (res) => {
    console.log("res", res);
    addressList.value = res.data.children;
    provinces.value = addressList.value || [];
    citys.value = provinces.value[0].children || [];
    if (citys.value[0].children) {
      districts.value = citys.value[0].children || [];
    } else {
      districts.value = [];
    }
    curList.value = [provinces.value, citys.value, districts.value];
    getMyAddress();
  },
});
// 获取我的列表
function getMyAddress() {
  uni.showLoading({
    title: '加载中',
    mask: true
  })
  getAddress().then((res) => {
    console.log("res", res);
    list.value = res.data.list;
  }).finally(() => {
    uni.hideLoading();
  })
}

function changeAddress(e) {
  console.log("e", e);
  switch (e.columnIndex) {
    case 0:
      console.log("e.value[0].children", e.value[0].children);
      citys.value = e.value[0].children || [];
      if (citys.value[0].children) {
        districts.value = citys.value[0].children;
      } else {
        districts.value = [];
      }
      curList.value = [provinces.value, citys.value, districts.value];
      break;
    case 1:
      if (e.value[1].children) {
        districts.value = e.value[1].children;
      } else {
        districts.value = [];
      }
      curList.value = [provinces.value, citys.value, districts.value];
      break;
    case 2:
      break;
  }
}
function confirmAddress(e) {
  formData.value.city.province = e.value[0].name;
  formData.value.city.provinceCode = e.value[0].adcode;
  formData.value.city.city = e.value[1].name;
  formData.value.city.cityCode = e.value[1].adcode;
  if (e.value[2]) {
    formData.value.city.district = e.value[2].name;
    formData.value.city.districtCode = e.value[2].adcode;
    formData.value.city.code = e.value[2].adcode;
  } else {
    formData.value.city.district = '';
    formData.value.city.districtCode = '';
    formData.value.city.code = '';
  }
}
</script>
<style scoped lang="scss">
.flex-1 {
  overflow-x: hidden;
}
.address {
  font-size: 28rpx;
  padding: 0 24rpx;
  color: rgba(0, 0, 0, 0.65);
  
  &-item {
    background-color: #fff;
    border-radius: 8rpx;
    position: relative;
    padding: 24rpx;
    margin-top: 24rpx;
  }
  &-detail {
    font-size: 32rpx;
    color: rgba(0, 0, 0, 0.85);
    padding: 24rpx 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    margin-bottom: 24rpx;
  }
  &-name {
    width: 180rpx;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .text {
    margin-left: 12rpx;
    &.act {
      color: $uni-color-primary;
    }
  }
  .close {
    position: absolute;
    padding: 24rpx;
    right: 0;
    top: 0;
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
