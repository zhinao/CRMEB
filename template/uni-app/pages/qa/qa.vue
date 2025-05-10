<template>
	<view class="container">
		<view class="header">
			<text class="title">洗护产品调查问卷</text>
		</view>

		<!-- 基础信息收集 -->
		<view class="section">
			<text class="section-title">一、基础信息收集</text>
			<view class="question">
				<text class="question-title">头发长度：</text>
				<radio-group @change="updateAnswer('hairLength', $event.detail.value)">
					<label class="radio-item" v-for="(option, index) in hairLengthOptions" :key="index">
						<radio :value="option" /> {{ option }}
					</label>
				</radio-group>
			</view>

			<view class="question">
				<text class="question-title">头发物理性质：</text>
				<radio-group @change="updateAnswer('hairTexture', $event.detail.value)">
					<label class="radio-item" v-for="(option, index) in hairTextureOptions" :key="index">
						<radio :value="option" /> {{ option }}
					</label>
				</radio-group>
			</view>

			<view class="question">
				<text class="question-title">头发类型：</text>
				<radio-group @change="updateAnswer('hairType', $event.detail.value)">
					<label class="radio-item" v-for="(option, index) in hairTypeOptions" :key="index">
						<radio :value="option" /> {{ option }}
					</label>
				</radio-group>
			</view>
		</view>

		<!-- 头皮状况诊断 -->
		<view class="section">
			<text class="section-title">二、头皮状况诊断</text>
			<view class="question">
				<text class="question-title">头皮类型：</text>
				<radio-group @change="updateAnswer('scalpType', $event.detail.value)">
					<label class="radio-item" v-for="(option, index) in scalpTypeOptions" :key="index">
						<radio :value="option" /> {{ option }}
					</label>
				</radio-group>
			</view>

			<view class="question">
				<text class="question-title">头皮屑情况：</text>
				<radio-group @change="updateAnswer('dandruff', $event.detail.value)">
					<label class="radio-item" v-for="(option, index) in dandruffOptions" :key="index">
						<radio :value="option" /> {{ option }}
					</label>
				</radio-group>
			</view>
		</view>

		<!-- 护发问题筛查 -->
		<view class="section">
			<text class="section-title">三、护发问题筛查</text>
			<view class="question">
				<text class="question-title">头发易打结/断裂：</text>
				<radio-group @change="updateAnswer('hairBreakage', $event.detail.value)">
					<label class="radio-item" v-for="(option, index) in yesNoOptions" :key="index">
						<radio :value="option" /> {{ option }}
					</label>
				</radio-group>
			</view>

			<view class="question">
				<text class="question-title">洗发频率：</text>
				<radio-group @change="updateAnswer('washFrequency', $event.detail.value)">
					<label class="radio-item" v-for="(option, index) in washFrequencyOptions" :key="index">
						<radio :value="option" /> {{ option }}
					</label>
				</radio-group>
			</view>
		</view>

		<!-- 提交按钮 -->
		<view class="submit-section">
			<button class="submit-button" @click="submitAnswers">提交问卷</button>
		</view>


<view class="popup-container" v-if="visible" @touchmove.stop.prevent>
    <view class="popup-mask" @click="close"></view>
    <view class="popup-content">
      <view class="popup-header">
        <text class="popup-title">推荐方案</text>
        <text class="popup-close" @click="close">×</text>
      </view>
      
      <view class="product-info">
        <image class="product-image" :src="product.image" mode="aspectFit"></image>
        <view class="product-details">
          <text class="product-name">{{ product.store_name }}</text>
          <text class="product-price">¥{{ product.price }}</text>
        </view>
      </view>
      
      <view class="advice-section">
        <text class="section-title">护发建议</text>
        <text class="advice-content">{{ product.advice }}</text>
      </view>
      
      <button class="confirm-btn" @click="confirm">立即购买</button>
    </view>
  </view>
  


	</view>
</template>

<script>
	import { getProductDetail} from '@/api/store.js';
	
	export default {
		data() {
			return {
				visible:false,
				product:{
					store_name: "",
					image: "",
					price: "",
					advice: ""
				},
				answers: {},
				// 问卷选项
				hairLengthOptions: ["中长发", "短发", "寸头", "光头"],
				hairTextureOptions: ["细软发", "粗硬发", "不粗不细"],
				hairTypeOptions: [
					"容易出油，触摸有油腻感",
					"发丝干枯、毛躁、分叉、打结",
					"不干不油，柔软顺滑有光泽",
				],
				scalpTypeOptions: [
					"头皮油（非常油腻、伴有脂溢性皮炎）",
					"头皮干",
					"头皮不干不油",
					"不清楚自身头皮类型",
				],
				dandruffOptions: [
					"头皮屑多（易脱落如雪花 / 粘附头皮难脱落）",
					"头皮屑少",
				],
				yesNoOptions: ["是", "否"],
				washFrequencyOptions: [
					"每天或隔天洗（易出油）",
					"每周2-3次（中性）",
					"每周1次或更少（干性）",
				],
			};
		},
		methods: {
			close()
			{
				this.visible=false;
			},
			updateAnswer(question, value) {
				this.answers[question] = value;
			},
			submitAnswers() {
				console.log("用户回答：", this.answers);
				// 根据回答跳转逻辑
				this.recommendProducts();
			},
			recommendProducts() {
				
				const productIds=[92,91,90,89,88];//护发素,短发重油洗发膏,油性洗发膏,中性洗发膏,干性洗发膏
				
				getProductDetail(92).then(res => {
					
					this.visible=true;
					this.product=res.data.storeInfo;
					this.product.advice="及时清洁，水温≤40℃";
				});
				
				
				
				
				// const {
				// 	hairType,
				// 	washFrequency
				// } = this.answers;

				// if (hairType === "容易出油，触摸有油腻感") {
				// 	uni.showModal({
				// 		title: "推荐方案",
				// 		content: "推荐产品：油性洗头膏、短发重油洗头膏\n护发建议：及时清洁，水温≤40℃...",
				// 	});
				// } else if (hairType === "发丝干枯、毛躁、分叉、打结") {
				// 	uni.showModal({
				// 		title: "推荐方案",
				// 		content: "推荐产品：干性/中性洗头膏\n护发建议：洗发前预涂护发素...",
				// 	});
				// } else if (hairType === "不干不油，柔软顺滑有光泽") {
				// 	uni.showModal({
				// 		title: "推荐方案",
				// 		content: "推荐产品：中性/油性洗头膏\n护发建议：定期头皮按摩...",
				// 	});
				// }
			},
		},
	};
</script>

<style>
	.container {
		padding: 20rpx;
		background-color: #f9f9f9;
	}

	.header {
		text-align: center;
		margin-bottom: 20rpx;
	}

	.title {
		font-size: 36rpx;
		font-weight: bold;
		color: #333;
	}

	.section {
		margin-bottom: 30rpx;
		background-color: #fff;
		padding: 20rpx;
		border-radius: 10rpx;
		box-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.1);
	}

	.section-title {
		font-size: 30rpx;
		font-weight: bold;
		margin-bottom: 10rpx;
		color: #555;
	}

	.question {
		margin-bottom: 20rpx;
	}

	.question-title {
		font-size: 28rpx;
		margin-bottom: 10rpx;
		color: #666;
	}

	.radio-item {
		display: flex;
		align-items: center;
		margin-bottom: 10rpx;
	}

	.submit-section {
		text-align: center;
		margin-top: 20rpx;
	}

	.submit-button {
		background-color: #007aff;
		color: #fff;
		padding: 15rpx 30rpx;
		border-radius: 5rpx;
		font-size: 28rpx;
	}
	
	
	.popup-container {
	  position: fixed;
	  top: 0;
	  left: 0;
	  right: 0;
	  bottom: 0;
	  z-index: 999;
	  display: flex;
	  justify-content: center;
	  align-items: center;
	}
	
	.popup-mask {
	  position: absolute;
	  top: 0;
	  left: 0;
	  right: 0;
	  bottom: 0;
	  background-color: rgba(0, 0, 0, 0.5);
	}
	
	.popup-content {
	  position: relative;
	  width: 80%;
	  max-width: 600rpx;
	  background-color: #fff;
	  border-radius: 16rpx;
	  overflow: hidden;
	  z-index: 1000;
	  padding-bottom: 30rpx;
	}
	
	.popup-header {
	  display: flex;
	  justify-content: space-between;
	  align-items: center;
	  padding: 20rpx 30rpx;
	  border-bottom: 1rpx solid #f5f5f5;
	}
	
	.popup-title {
	  font-size: 32rpx;
	  font-weight: bold;
	  color: #333;
	}
	
	.popup-close {
	  font-size: 40rpx;
	  color: #999;
	}
	
	.product-info {
	  display: flex;
	  padding: 30rpx;
	  align-items: center;
	}
	
	.product-image {
	  width: 150rpx;
	  height: 150rpx;
	  border-radius: 8rpx;
	  margin-right: 20rpx;
	}
	
	.product-details {
	  flex: 1;
	  display: flex;
	  flex-direction: column;
	}
	
	.product-name {
	  font-size: 28rpx;
	  color: #333;
	  margin-bottom: 10rpx;
	}
	
	.product-price {
	  font-size: 32rpx;
	  color: #f44;
	  font-weight: bold;
	}
	
	.advice-section {
	  padding: 0 30rpx;
	  margin-bottom: 30rpx;
	}
	
	.section-title {
	  display: block;
	  font-size: 28rpx;
	  color: #333;
	  font-weight: bold;
	  margin-bottom: 15rpx;
	}
	
	.advice-content {
	  font-size: 26rpx;
	  color: #666;
	  line-height: 1.6;
	}
	
	.confirm-btn {
	  margin: 0 30rpx;
	  background-color: #07c160;
	  color: white;
	  border-radius: 50rpx;
	  font-size: 28rpx;
	  height: 80rpx;
	  line-height: 80rpx;
	}


	
</style>