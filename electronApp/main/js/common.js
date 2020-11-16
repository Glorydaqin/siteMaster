function ajax(opts) {
    var xhr = new XMLHttpRequest(),
        type = opts.type || 'GET',
        url = opts.url,
        params = opts.data,
        dataType = opts.dataType || 'json';

    type = type.toUpperCase();

    if (type === 'GET') {
        params = (function(obj){
            var str = '';

            for(var prop in obj){
                str += prop + '=' + obj[prop] + '&'
            }
            str = str.slice(0, str.length - 1);
            return str;
        })(opts.data);
        url += url.indexOf('?') === -1 ? '?' + params : '&' + params;
    }

    xhr.open(type, url);

    if (opts.contentType) {
        xhr.setRequestHeader('Content-type', opts.contentType);
    }

    xhr.send(params ? params : null);

    //return promise
    return new Promise(function (resolve, reject) {
        //onload are executed just after the sync request is comple，
        //please use 'onreadystatechange' if need support IE9-
        xhr.onload = function () {
            if (xhr.status === 200) {
                var result;
                try {
                    result = JSON.parse(xhr.response);
                } catch (e) {
                    result = xhr.response;
                }
                resolve(result);
            } else {
                reject(xhr.response);
            }
        };

    });
}
