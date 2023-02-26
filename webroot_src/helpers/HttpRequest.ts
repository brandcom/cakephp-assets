import axios from "axios";

export default class HttpRequest
{
	public static post(url: string, data: object) {
		return axios.post(url, data, {
			xsrfCookieName: "csrfToken",
			xsrfHeaderName: "X-CSRF-Token",
		});
	}
}
