require('./bootstrap');

import Alpine from 'alpinejs';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import Dropzone from "dropzone";
import "dropzone/dist/dropzone.css";
import Swal from 'sweetalert2';
import Glide from '@glidejs/glide';
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

// Alpine
window.Alpine = Alpine;
Alpine.start();

// Glide
window.Glide = Glide

// Swiper
window.Swiper = Swiper

// CKEditor5
window.ClassicEditor = ClassicEditor

// Dropzone
window.Dropzone = Dropzone

// SweetAlert2
window.Swal = Swal
