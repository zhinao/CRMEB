<template>
	<view class='video-swiper'>
		<video 
			class="video-player" 
			:src="currentVideo.src"
			:poster="currentVideo.poster"
			:autoplay="autoplay" 
			:loop="false"
			:muted="true"
			:controls="true"
			:show-fullscreen-btn="true"
			:show-play-btn="true"
			:show-center-play-btn="true"
			@click="goToLink(currentVideo)"
			@ended="onVideoEnded"
			@play="onVideoPlay"
			@pause="onVideoPause"
		></video>
		<view class="dots acea-row" v-if="videoUrls.length > 1">
			<view class="dot" :class="index == currentIndex ? 'active' : ''" 
				v-for="(item,index) in videoUrls" :key="index" 
				@click="switchVideo(index)"></view>
		</view>
	</view>
</template>

<script>
	export default {
		props: {
			imgUrls: {
				type: Array,
				default: function(){
					return [];
				}
			}
		},
		data() {
			return {
				circular: true,
				autoplay: true,
				interval: 8000, // 增加间隔时间以适应视频播放
				duration: 500,
				currentIndex: 0,
				videoUrls: [],
				autoSwitchTimer: null,
				isPlaying: false,
			};
		},
		computed: {
			currentVideo() {
				if (this.videoUrls.length > 0) {
					const video = this.videoUrls[this.currentIndex];
					return {
						src: video.img, // 假设视频URL保存在img字段中
						poster: video.poster || video.img, // 视频封面图
						link: video.link
					};
				}
				return {};
			}
		},
		watch: {
			imgUrls: {
				immediate: true,
				handler(newVal) {
					// 将图片数据转换为视频数据
					this.videoUrls = newVal.map(item => ({
						...item,
						src: item.img,
						poster: item.poster || item.img
					}));
					this.startAutoSwitch();
				}
			}
		},
		methods: {
			// 视频播放结束时切换到下一个视频
			onVideoEnded() {
				if (this.videoUrls.length > 1) {
					this.switchToNext();
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
			// 点击视频跳转链接
			goToLink(video) {
				if (video.link) {
					uni.navigateTo({
						url: video.link
					});
				}
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

<style scoped lang="scss">
	.video-swiper {
		width: 100%;
		height: 100vh;
		min-height: 100vh;
		position: relative;
		border-radius: 0;
		overflow: hidden;

		.video-player {
			width: 100%;
			height: 100%;
			min-height: 100vh;
			background-color: #000;
			object-fit: cover;
		}

		.dots {
			position: absolute;
			right: 40rpx;
			bottom: 20rpx;
			padding: 6rpx 12rpx;
			background: rgba(0, 0, 0, 0.3);
			border-radius: 20rpx;

			.dot {
				width: 12rpx;
				height: 12rpx;
				border: 2rpx solid rgba(255, 255, 255, 0.6);
				border-radius: 50%;
				margin-right: 15rpx;
				cursor: pointer;
				transition: all 0.3s ease;

				&:last-child {
					margin-right: 0;
				}

				&.active {
					border-color: #e93323;
					background-color: #e93323;
					transform: scale(1.2);
				}

				&:hover {
					border-color: rgba(255, 255, 255, 0.8);
				}
			}
		}
	}
</style>