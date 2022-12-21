export function addUriParams(uri, params) {
  let result = uri;
  let delimiter = result.includes('?') ? '&' : '?';

  for (const [key, value] of Object.entries(params)) {
    result += `${delimiter}${encodeURIComponent(key)}=${encodeURIComponent(
      value,
    )}`;
    delimiter = '&';
  }

  return result;
}
