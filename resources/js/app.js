require('./bootstrap');

import Alpine from 'alpinejs';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import Dropzone from "dropzone";
import "dropzone/dist/dropzone.css";

window.Alpine = Alpine;
Alpine.start();

// CKEditor5
ClassicEditor
    .create(document.querySelector('#editor'))
    .then(editor => {
        console.log(editor);
    })
    .catch(error => {
        console.error(error);
    });

// Dropzone
window.Dropzone = Dropzone
window.myDropzone = new Dropzone("#my-dropzone", {
    headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    dictDefaultMessage: "Mueva una imagen al recuadro",
    acceptedFiles: 'image/*',
    paramName: "file", // The name that will be used to transfer the file
    maxFilesize: 2, // MB
    complete: function (file) {
        this.removeFile(file);
    },
    queuecomplete: function () {
        Livewire.emit('refreshProduct');
    }
});



