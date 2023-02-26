import HttpRequest from "../../../../helpers/HttpRequest";

export default class FilePoolRequest
{
	private static baseUrl : string = '/admin/assets/file-pool/';

	public static query(data: object) {
		return this.buildRequest('query', data);
	}

	public static getAsset(data: object) {
		return this.buildRequest('getAsset', data);
	}

	private static buildRequest(endpoint: string, data: object) {
		return HttpRequest.post(this.baseUrl + endpoint, data);
	}
}
