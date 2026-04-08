<template>
	<view class="index-container">
		<diy ref="diy" v-if="isDiy && loading"></diy>
		<visualization ref="vis" v-else-if="!isDiy && loading"></visualization>
		<!-- 按钮容器 - 垂直排列 -->
		<view style="position: fixed; bottom: 200rpx; right: 30rpx; z-index: 999;">
			<!-- 注册按钮 -->
			<button class="float-btn" style="background-color: #fc4141; color: white; width: 120rpx; height: 120rpx; margin-bottom: 20rpx; border-radius: 50rpx; display: flex; align-items: center; justify-content: center; box-shadow: 0 4rpx 20rpx rgba(252, 65, 65, 0.4); font-weight: bold;" @click="goToUserPage">
				<text>注册</text>
			</button>
			<!-- 浮动分享按钮 -->
			<button open-type="share"  class="float-btn" style="background-color: #fc4141; color: white; width: 120rpx; height: 120rpx; border-radius: 50rpx; display: flex; align-items: center; justify-content: center; box-shadow: 0 4rpx 20rpx rgba(252, 65, 65, 0.4); font-weight: bold;" @click="shareToWechat">
				<text>分享</text>
			</button>
		</view>
	</view>
</template>

<script>
import diy from './diy';
import visualization from './visualization';
import Cache from '@/utils/cache';
import { getShare, getVersion } from '@/api/public.js';
import { spreadAgent } from '@/api/user.js';
export default {
	data() {
		return {
			isDiy: uni.getStorageSync('is_diy'),
			shareInfo: {},
			loading: false
		};
	},
	components: {
		diy,
		visualization
	},
	onLoad(options) {
		uni.hideTabBar();
		//扫码携带参数处理
		// #ifdef MP
		const queryData = uni.getEnterOptionsSync(); // uni-app版本 3.5.1+ 支持
		if (queryData.query.scene){
			 this.$Cache.set('agent_id', queryData.query.scene);
		}
		// #endif
		// #ifndef MP
		if (options.agent_id) {
			this.$Cache.set('agent_id', options.agent_id);
		}
		// #endif
		this.setOpenShare();
	},
	onShow() {
		this.getVersion(0);
		if(this.$Cache.get('agent_id')){
			this.bindAgent();
		}
	},
	onHide() {
		this.$Cache.clear('agent_id');
	},
	methods: {
		// 跳转到用户页面
		goToUserPage() {
			uni.switchTab({
				url: '/pages/user/index'
			});
		},
		// 分享给微信好友
		shareToWechat() {
			console.log('shareToWechat 1');
			// #ifdef MP
			
			uni.showShareMenu({
				withShareTicket: true,
				menus: ['shareAppMessage']
			});
			
			// #endif
			console.log('shareToWechat 2');
			
			// #ifdef APP-PLUS
			uni.share({
				provider: 'weixin',
				scene: 'WXSceneSession', // WXSceneSession为分享到微信好友，WXSceneTimeline为分享到朋友圈
				type: 0,
				title: this.shareInfo.title || '商城分享',
				summary: this.shareInfo.synopsis || '欢迎使用我们的商城',
				imageUrl: this.shareInfo.img || '',
				href: '',
				success: function(res) {
					uni.showToast({
						title: '分享成功',
						icon: 'success'
					});
				},
				fail: function(err) {
					uni.showToast({
						title: '分享失败',
						icon: 'none'
					});
				}
			});
			// #endif
			
			// #ifdef H5
			if (this.$wechat.isWeixin()) {
				uni.showToast({
					title: '请点击右上角分享',
					icon: 'none'
				});
			} else {
				uni.showToast({
					title: '请在微信中打开',
					icon: 'none'
				});
			}
			// #endif
		},
		// 绑定员工关系
		bindAgent(agent_id) {
			spreadAgent({
				// #ifdef MP
				agent_code: this.$Cache.get('agent_id')
				// #endif
				// #ifndef MP
				agent_id: this.$Cache.get('agent_id')
				// #endif
			}).then((res) => {
				this.$Cache.clear('agent_id');
				uni.showToast({
					icon: 'none',
					title: res.msg,
					duration: 3000
				});
			});
		},
		getVersion(name) {
			uni.$emit('uploadFooter');
			getVersion(name)
				.then((res) => {
					this.version = res.data.version;
					this.isDiy = res.data.is_diy;
					this.loading = true;
					uni.setStorageSync('is_diy', res.data.is_diy);
					if (!uni.getStorageSync('DIY_VERSION') || res.data.version != uni.getStorageSync('DIY_VERSION')) {
						if (uni.getStorageSync('DIY_VERSION')) {
							uni.setStorageSync('DIY_VERSION', res.data.version);
							if (this.isDiy) {
								this.$refs.diy.reconnect();
							} else {
								this.$refs.vis.reconnect();
							}
						}
						uni.setStorageSync('DIY_VERSION', res.data.version);
					} else {
					}
				})
				.catch((err) => {
					// #ifdef APP-PLUS
					setTimeout((e) => {
						this.getVersion(0);
					}, 1500);
					// #endif
					// #ifndef APP-PLUS
					this.$util.Tips({
						title: err
					});
					// #endif
				});
		},
		// 微信分享；
		setOpenShare: function () {
			let that = this;
			getShare().then((res) => {
				let data = res.data;
				this.shareInfo = data;
				// #ifdef H5
				let url = location.href;
				if (this.$store.state.app.uid) {
					url = url.indexOf('?') === -1 ? url + '?spread=' + this.$store.state.app.uid : url + '&spread=' + this.$store.state.app.uid;
				}
				if (that.$wechat.isWeixin()) {
					let configAppMessage = {
						desc: data.synopsis,
						title: data.title,
						link: url,
						imgUrl: data.img
					};
					that.$wechat.wechatEvevt(['updateAppMessageShareData', 'updateTimelineShareData'], configAppMessage);
				}
				// #endif
			});
		}
	},
	onReachBottom: function () {
		if (this.isDiy) {
			this.$refs.diy.onsollBotton();
		}
	},
	// 滚动监听
	onPageScroll(e) {
		// 传入scrollTop值并触发所有easy-loadimage组件下的滚动监听事件
		uni.$emit('scroll');
	},
	// #ifdef MP
	//发送给朋友
	onShareAppMessage(res) {
		// 此处的distSource为分享者的部分信息，需要传递给其他人
		let that = this;
		return {
			title: this.shareInfo.title,
			path: '/pages/index/index?spid=' + this.$store.state.app.uid || 0,
			imageUrl: this.shareInfo.img
		};
	},
	//分享到朋友圈
	onShareTimeline() {
		return {
			title: this.shareInfo.title,
			query: {
				spid: this.$store.state.app.uid || 0
			},
			imageUrl: this.shareInfo.img
		};
	}
	// #endif
};
</script>

<style>
.float-share-btn {
	position: fixed;
	right: 20rpx;
	bottom: 200rpx;
	width: 90rpx;
	height: 90rpx;
	background: linear-gradient(to right, #1AAD19, #07C160);
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	box-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.2);
	z-index: 999;
}

.float-share-btn .iconfont {
	color: #ffffff;
	font-size: 50rpx;
}
</style>
