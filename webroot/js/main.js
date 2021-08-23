"use strict";

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[autoopen]').forEach(item => {
        let modal = new bootstrap.Modal(item);
        modal.show();
    });

    let toastElList = [].slice.call(document.querySelectorAll('.toast'));
    let toastList = toastElList.map(function (toastEl) {
	let toast = new bootstrap.Toast(toastEl, {'animation': true, 'delay': 10000});
	toast.show();
    });
});

