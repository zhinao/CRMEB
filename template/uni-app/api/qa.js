import request from "@/utils/request.js";
export function recommendProducts(data) {
	console.log('recommendProducts');
	return request.post('qa/recommendProducts', data, {
		noAuth: true
	});
}
export function initWenjuan() {
	console.log('initWenjuan');
	return request.get('qa/wenjuan?t='+ Date.now(), null,{
		noAuth: true
	});
}
