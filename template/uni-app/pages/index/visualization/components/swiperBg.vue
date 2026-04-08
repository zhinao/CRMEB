<template>
	<view class="swiperBg" :style="{marginTop:mt +'rpx'}">
		<view class="bag" v-if="isIframe || (videoUrls.length && isShow)">
		</view>
		<block v-if="isShow && videoUrls.length">
			<view class="video-container square" v-if="videoUrls.length">
				<video 
					class="video-player skeleton-rect" 
					:style="'height:'+ (videoH) +'rpx;'" 
					:src="currentVideo.src"
					:poster="currentVideo.poster"
					:autoplay="true"
					:loop="false"
					:muted="true"
					:controls="true"
					:show-fullscreen-btn="true"
					:show-play-btn="true"
					:show-center-play-btn="true"
					@click="goDetail(currentVideo)"
					@ended="onVideoEnded"
					@play="onVideoPlay"
					@pause="onVideoPause"
				></video>
				
				<!-- 视频切换指示器 -->
				<view class="video-indicators" v-if="videoUrls.length > 1">
					<view 
						class="indicator-dot square" 
						:class="{active: index == currentIndex}"
						v-for="(item,index) in videoUrls" 
						:key="index"
						@click="switchVideo(index)"
					></view>
				</view>
			</view>
		</block>
		<block v-if="!isShow && isIframe && videoUrls.length && videoH">
			<view class="video-container square" v-if="videoUrls.length && videoH" :style="'height:'+ (videoH) +'rpx;'">
				<video 
					class="video-player" 
					<!-- :style="'height:'+ (videoH) +'rpx;'"  -->
					
					style="height: 100%;"

					:src="currentVideo.src"
					:poster="currentVideo.poster"
					:autoplay="true"
					:loop="false"
					:muted="true"
					:controls="true"
					:show-fullscreen-btn="true"
					:show-play-btn="true"
					:show-center-play-btn="true"
					@click="goDetail(currentVideo)"
					@ended="onVideoEnded"
				></video>
				
				<!-- 视频切换指示器 -->
				<view class="video-indicators" v-if="videoUrls.length > 1">
					<view 
						class="indicator-dot square" 
						:class="{active: index == currentIndex}"
						v-for="(item,index) in videoUrls" 
						:key="index"
						@click="switchVideo(index)"
					></view>
				</view>
			</view>
		</block>
		<block v-if="isIframe && (!videoUrls.length || !videoH)">
			<view class="empty-img">{{$t(`暂无视频，请上传视频`)}}</view>
		</block>
	</view>
</template>

<script>
	let statusBarHeight = uni.getSystemInfoSync().statusBarHeight + 'px';
	let app = getApp();
	import {
		goPage
	} from '@/libs/order.js'
	export default {
		name: 'swiperBg',
		props: {
			dataConfig: {
				type: Object,
				default: () => {}
			},
		},
		watch: {
			dataConfig: {
				immediate: true,
				handler(nVal, oVal) {
					if (nVal) {
						// 将图片数据转换为视频数据
						this.videoUrls = nVal.imgList ? nVal.imgList.list.map(item => ({
							...item,
							src: item.img, // 假设视频URL保存在img字段中
							poster: item.poster || item.img // 视频封面图
						})) : [];
						this.isShow = nVal.isShow ? nVal.isShow.val : true
						// 设置默认视频高度
						this.$set(this, 'videoH', 720);
						// 启动自动切换
						this.startAutoSwitch();
					}
				}
			},
			videoH(nVal, oVal) {
				let self = this
			},
		},
		data() {
			return {
				indicatorDots: false,
				circular: true,
				autoplay: true,
				interval: 8000, // 增加间隔时间以适应视频播放
				duration: 500,
				videoUrls: [], // 视频数据
				name: this.$options.name,
				isIframe: false,
				mt: -55,
				isShow: true,
				videoH: 720,
				currentIndex: 0,
				autoSwitchTimer: null,
				isPlaying: false,
			};
		},
		computed: {
			currentVideo() {
				if (this.videoUrls.length > 0) {
					const video = this.videoUrls[this.currentIndex];
					return {
						src: video.src,
						poster: video.poster || '',
						info: video.info
					};
				}
				return {};
			}
		},
		created() {
			// #ifdef MP || APP-PLUS
			const res = uni.getSystemInfoSync()
			const system = res.platform
			this.statusBarHeight = res.statusBarHeight
			if (system === 'android') {
				this.mt = parseFloat(statusBarHeight) * 2 + 170
			} else {
				this.mt = parseFloat(statusBarHeight) * 2 + 168
			}

			// #endif
			this.isIframe = app.globalData.isIframe;
		},
		mounted() {},
		methods: {
			goDetail(url) {
				goPage().then(res => {
					let urls = url.info[1].value
					this.$util.JumpPath(urls);
				})
			},
			//替换安全域名
			setDomain: function(url) {
				url = url ? url.toString() : '';
				//本地调试打开,生产请注销
				if (url.indexOf("https://") > -1) return url;
				else return url.replace('http://', 'https://');
			},
			// 视频播放结束时切换到下一个视频
			onVideoEnded() {
				if (this.videoUrls.length > 1) {
					this.switchToNext();
				} else {
					// 如果只有一个视频，重新播放
					this.restartCurrentVideo();
				}
			},
			// 视频开始播放
			onVideoPlay() {
				this.isPlaying = true;
				// 视频播放时暂停自动切换
				this.stopAutoSwitch();
			},
			// 视频暂停播放
			onVideoPause() {
				this.isPlaying = false;
				// 视频暂停时恢复自动切换
				this.startAutoSwitch();
			},
			// 切换到指定视频
			switchVideo(index) {
				if (index !== this.currentIndex) {
					this.currentIndex = index;
					this.restartAutoSwitch();
				}
			},
			// 切换到下一个视频
			switchToNext() {
				this.currentIndex = (this.currentIndex + 1) % this.videoUrls.length;
			},
			// 重新播放当前视频
			restartCurrentVideo() {
				// 这里可以通过ref调用video的play方法，但uni-app中需要特殊处理
				console.log('重新播放当前视频');
			},
			// 启动自动切换
			startAutoSwitch() {
				if (this.videoUrls.length > 1 && !this.isPlaying) {
					this.stopAutoSwitch(); // 确保不会重复设置定时器
					this.autoSwitchTimer = setInterval(() => {
						if (!this.isPlaying) {
							this.switchToNext();
						}
					}, this.interval);
				}
			},
			// 重启自动切换
			restartAutoSwitch() {
				this.stopAutoSwitch();
				this.startAutoSwitch();
			},
			// 停止自动切换
			stopAutoSwitch() {
				if (this.autoSwitchTimer) {
					clearInterval(this.autoSwitchTimer);
					this.autoSwitchTimer = null;
				}
			}
		},
		beforeDestroy() {
			this.stopAutoSwitch();
		}
	}
</script>

<style lang="scss">
	.swiperBg {
		background-color: #fff;
		position: relative;
		margin-top: -20rpx;
		padding-top: 4rpx;

		.bag {
			position: absolute;
			top: -140rpx; /* 移出可视区域 */
			width: 100%;
			height: 140rpx;
			background: linear-gradient(90deg, var(--view-main-start) 0%, var(--view-main-over) 100%);
			border-bottom-left-radius: 40rpx;
			border-bottom-right-radius: 40rpx;
			z-index: -1; /* 确保在视频下方 */
		}

		.colorBg {
			position: absolute;
			left: 0;
			top: -130rpx; /* 移出可视区域 */
			height: 130rpx;
			width: 100%;
			z-index: -1; /* 确保在视频下方 */
		}

		.video-container {
			z-index: 100;
			position: relative;
			min-height: 100vh;
			height: 100vh;
			padding: 0 $uni-index-margin-col;
			overflow: hidden;
			border-radius: 10rpx;

			.video-player {
				width: 100%;
				height: 100vh;
				min-height: 100vh;
				border-radius: 10rpx;
				background-color: #000;
				object-fit: cover;
			}

			.video-indicators {
				position: absolute;
				bottom: 20rpx;
				left: 50%;
				transform: translateX(-50%);
				display: flex;
				gap: 8rpx;
				z-index: 200;
				padding: 6rpx 12rpx;
				background: rgba(0, 0, 0, 0.3);
				border-radius: 20rpx;

				.indicator-dot {
					width: 20rpx;
					height: 5rpx;
					border-radius: 3rpx;
					background: rgba(255, 255, 255, 0.4);
					cursor: pointer;
					transition: all 0.3s ease;

					&.active {
						background: #fff;
						transform: scale(1.2);
					}

					&:hover {
						background: rgba(255, 255, 255, 0.8);
					}
				}
			}

			// 方形指示点样式
			&.square {
				.indicator-dot {
					width: 20rpx;
					height: 5rpx;
					border-radius: 3rpx;
				}
			}
		}
	}

	.empty-img {
		width: 690rpx;
		height: 300rpx;
		border-radius: 14rpx;
		margin: 26rpx auto 0 auto;
		background-color: #ccc;
		text-align: center;
		line-height: 300rpx;
		position: relative;
		color: #666;
		font-size: 28rpx;

		.iconfont {
			font-size: 50rpx;
		}
	}
</style>