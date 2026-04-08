<template>
	<view class="swiperBg skeleton-rect" :style="'margin-top:' + marginTop*2 +'rpx;'" v-show="!isSortType">
		<block v-if="videoUrls.length">
			<view class="colorBg"
				:style="'background: linear-gradient(90deg, '+ bgColor[0].item +' 50%, '+ bgColor[1].item +' 100%);'"
				v-if="isColor"></view>
			<view class="video-wrapper" :class="[imgConfig?'':'fillet']" :style="'padding: 0 '+ paddinglr +'rpx;'">
				<video 
					class="video-player" 
					style="height:100vh;"
					src="http://cdn.danao.net.cn/logo.mp4"
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
				
				<!-- 自定义指示器 -->
				<view v-if="docConfig==0" class="dot acea-row"
					:style="{paddingLeft: paddinglr+20 + 'rpx',paddingRight: paddinglr+20 + 'rpx',justifyContent: (txtStyle==1?'center':txtStyle==2?'flex-end':'flex-start')}">
					<view class="dot-item" :style="active==index?'background:'+ dotColor:''"
						v-for="(item,index) in videoUrls" :key="index" @click="switchVideo(index)"></view>
				</view>
				<view v-if="docConfig==1" class="dot acea-row"
					:style="{paddingLeft: paddinglr+20 + 'rpx',paddingRight: paddinglr+20 + 'rpx',justifyContent: (txtStyle==1?'center':txtStyle==2?'flex-end':'flex-start')}">
					<view class="dot-item line_dot-item" :style="active==index?'background:'+ dotColor:''"
						v-for="(item,index) in videoUrls" :key="index" @click="switchVideo(index)"></view>
				</view>
				<view v-if="docConfig==2" class="dot acea-row"
					:style="{paddingLeft: paddinglr+20 + 'rpx',paddingRight: paddinglr+20 + 'rpx',justifyContent: (txtStyle==1?'center':txtStyle==2?'flex-end':'flex-start')}">
					<view class="instruct">{{current}}/{{videoUrls.length}}</view>
				</view>
			</view>
		</block>
	</view>
</template>

<script>
	export default {
		name: 'swiperBg',
		props: {
			dataConfig: {
				type: Object,
				default: () => {}
			},
			isSortType: {
				type: String | Number,
				default: 0
			}
		},
		data() {
			return {
				circular: true,
				autoplay: true,
				interval: 8000, // 增加间隔时间以适应视频播放
				duration: 500,
				videoUrls: [], // 视频数据
				bgColor: this.dataConfig.bgColor.color, //轮播背景颜色
				marginTop: this.dataConfig.mbConfig.val, //组件上边距
				paddinglr: (this.dataConfig.lrConfig.val) * 2, //轮播左右边距
				docConfig: this.dataConfig.docConfig.type, //指示点样式
				imgConfig: this.dataConfig.imgConfig.type, //是否为圆角
				videoH: 2000,
				isColor: this.dataConfig.isShow.val,
				txtStyle: this.dataConfig.txtStyle.type,
				dotColor: this.dataConfig.dotColor.color[0].item,
				current: 1, //数字指示器当前
				active: 0, //一般指示器当前
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
		watch: {
			currentIndex(newIndex) {
				this.active = newIndex;
				this.current = newIndex + 1;
			}
		},
		created() {
			// 将图片数据转换为视频数据
			this.videoUrls = this.dataConfig.swiperConfig.list.map(item => ({
				...item,
				src: item.img, // 假设视频URL保存在img字段中
				poster: item.poster || item.img // 视频封面图
			}));
		},
		mounted() {
			if (this.videoUrls.length) {
				// 设置默认视频高度
				//this.$set(this, 'videoH', 720);
				// 启动自动切换
				this.startAutoSwitch();
			}
		},
		methods: {
			//替换安全域名
			setDomain: function(url) {
				url = url ? url.toString() : '';
				//本地调试打开,生产请注销
				if (url.indexOf("https://") > -1) return url;
				else return url.replace('http://', 'https://');
			},
			goDetail(url) {
				let urls = url.info[1].value
				this.$util.JumpPath(urls);
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
		position: relative;

		.colorBg {
			position: absolute;
			left: 0;
			top: -130rpx; /* 移出可视区域 */
			height: 130rpx;
			width: 100%;
			z-index: -1; /* 确保在视频下方 */
		}

		.video-wrapper {
			z-index: 20;
			position: relative;
			overflow: hidden;

			.video-player {
				width: 100%;
				height: 100vh;
				min-height: 100vh;
				display: block;
				background-color: #000;
				object-fit: cover;
			}

			.dot {
				position: absolute;
				left: 0;
				bottom: 20rpx;
				width: 100%;

				.instruct {
					width: 50rpx;
					height: 36rpx;
					line-height: 36rpx;
					background-color: #bfc1c4;
					color: #fff;
					border-radius: 16rpx;
					font-size: 24rpx;
					text-align: center;
				}

				.dot-item {
					width: 10rpx;
					height: 10rpx;
					background: rgba(0, 0, 0, .4);
					border-radius: 50%;
					margin: 0 4px;
					cursor: pointer;
					transition: all 0.3s ease;

					&.line_dot-item {
						width: 20rpx;
						height: 5rpx;
						border-radius: 3rpx;
					}

					&:hover {
						background: rgba(0, 0, 0, .6);
					}
				}
			}

			/* 设置圆角 */
			&.fillet {
				border-radius: 10rpx;

				.video-player {
					border-radius: 10rpx;
				}
			}
		}
	}
</style>