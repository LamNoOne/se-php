const sweetalertDeleteConfirmConfig = (title, content) => {
	return {
		title: title,
		text: content,
		showCancelButton: true,
		confirmButtonText: 'Delete',
		confirmButtonClass: 'btn btn-danger',
		cancelButtonClass: 'btn btn-cancel me-3 ms-auto',
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		buttonsStyling: !1,
		reverseButtons: true
	}
}
