<template>
	<view class="main">
		<guide v-if="guidePages" :advData="advData" :time="advData.time"></guide>
	</view>
</template>

<script>
import guide from '@/components/guide/index.vue';
import Cache from '@/utils/cache';
import { getOpenAdv } from '@/api/api.js';
export default {
	components: {
		guide
	},
	data() {
		return {
			guidePages: false,
			advData: [],
			indexUrl: '/pages/index/index'
		};
	},
	onLoad(options) {
		if (options.spid) {
			this.indexUrl = this.indexUrl + '?spid=' + options.spid;
		}
		this.loadExecution();
	},
	methods: {
		loadExecution() {
			const tagDate = uni.getStorageSync('guideDate') || '',
				nowDate = new Date().toLocaleDateString();
			if (tagDate === nowDate) {
				uni.switchTab({
					url: this.indexUrl
				});
				return;
			}
			getOpenAdv()
			.then((res) => {
				// 确保 res.data 存在
				if (!res || !res.data) {
					uni.switchTab({
						url: this.indexUrl
					});
					return;
				}
				// 检查是否有有效的广告数据
				const hasValidData = res.data.status && (res.data.value && res.data.value.length > 0 || res.data.video_link);
				if (hasValidData) {
					this.advData = res.data;
					uni.setStorageSync('guideDate', new Date().toLocaleDateString());
					this.guidePages = true;
				} else {
					// 如果没有有效数据，跳转到首页
					uni.switchTab({
						url: this.indexUrl
					});
				}
			})
			.catch((err) => {
				uni.switchTab({
					url: this.indexUrl
				});
			});
		}
	},
	onHide() {
		this.guidePages = false;
	}
};
</script>

<style>
page,
.main {
	width: 100%;
	height: 100%;
}
</style>
