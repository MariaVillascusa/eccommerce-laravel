require('./bootstrap');

import Alpine from 'alpinejs';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import Dropzone from "dropzone";
import "dropzone/dist/dropzone.css";
import Swal from 'sweetalert2';
import Glide from '@glidejs/glide'

// Alpine
window.Alpine = Alpine;
Alpine.start();

// Glide
window.Glide = Glide

// CKEditor5
window.ClassicEditor = ClassicEditor

// Dropzone
window.Dropzone = Dropzone

// SweetAlert2
window.Swal = Swal






