/*
 Template Name: Aplomb - Bootstrap 4 Admin Dashboard
 Author: Mannatthemes
 Website: www.mannatthemes.com
 File: Datatable js
 */
$(document).ready(function(){$("#datatable").DataTable(),$("#datatable-buttons").DataTable({lengthChange:!1,buttons:["copy","excel","pdf","colvis"], "language":{"url":"//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"}}).buttons().container().appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)"),$(document).ready(function(){$("#datatable2").DataTable()})});
