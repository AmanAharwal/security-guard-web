@if($hasPlugin('rateCalculate'))
    @push('scripts')
    <script>
        $(document).ready(function(){
            function calculateRates() {
                let regularRate = parseFloat($('input[name="regular_rate"]').val()) || 0;
                let laundryAllowance = parseFloat($('input[name="laundry_allowance"]').val()) || 0;
                const caninePremium = parseFloat(document.getElementById('canine_premium').value) || 0;
                const fireArmPremium = parseFloat(document.getElementById('fire_arm_premium').value) || 0;

                let total = regularRate + laundryAllowance + caninePremium + fireArmPremium;

                if (regularRate === 0 && laundryAllowance === 0 && caninePremium === 0 && fireArmPremium === 0) {
                    $('#gross_hourly_display').text('');
                    $('#normal_rate_display').text('');
                    $('#overtime_rate_display').text('');
                    $('#holiday_rate_display').text('');
                } else {
                    $('#gross_hourly_rate, #normal_rate').val(total.toFixed(2));
                    $('#gross_hourly_display, #normal_rate_display').text(`(${regularRate} + ${laundryAllowance} + ${caninePremium} + ${fireArmPremium}) = ${total.toFixed(2)}`);

                    let overtimeRate = ((regularRate + caninePremium) * 1.5) + laundryAllowance;
                    $('#overtime_rate').val(overtimeRate.toFixed(2));
                    $('#overtime_rate_display').text(`(${regularRate} + ${caninePremium} * 1.5) + ${laundryAllowance} = ${overtimeRate.toFixed(2)}`);

                    let holidayRate = ((regularRate + caninePremium) * 2) + laundryAllowance;
                    $('#holiday_rate').val(holidayRate.toFixed(2));
                    $('#holiday_rate_display').text(`(${regularRate} + ${caninePremium} * 2) + ${laundryAllowance} = ${holidayRate.toFixed(2)}`);
                }
            }

            $('.rate-calculate').on('input', calculateRates);
            calculateRates()
        });
    </script>
    @endpush
@endif

@if($hasPlugin('time'))
    @push('styles')
        <link rel="stylesheet" href="https://weareoutman.github.io/clockpicker/dist/jquery-clockpicker.min.css">
    @endpush
    @push('scripts')
    <script src="https://weareoutman.github.io/clockpicker/dist/jquery-clockpicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $("input[name=time]").clockpicker({       
                placement: 'bottom',
                align: 'left',
                autoclose: true,
                default: 'now',
                donetext: "Select",
            });
        });
    </script>
    @endpush
@endif

@if($hasPlugin('datePicker'))
    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr('.date-picker', {
                dateFormat: "Y-m-d",
                allowInput: true
            });
            flatpickr('.date-of-birth', {
                dateFormat: "Y-m-d",
                allowInput: true
            });
            flatpickr('.date_of_separation', {
                dateFormat: "Y-m-d",
                allowInput: true
            });
            flatpickr('.date-picker-punch-in', {
                dateFormat: "Y-m-d H:i:S", // Include time in the format
                allowInput: true,
                enableTime: true,       // Enable time selection
                time_24hr: true,       // Optional: use 24-hour format
                minuteIncrement: 1      // Optional: set minute increment
            });
            flatpickr('.date-picker-punch-out', {
                dateFormat: "Y-m-d H:i:S", // Include time in the format
                allowInput: true,
                enableTime: true,       // Enable time selection
                time_24hr: true,       // Optional: use 24-hour format
                minuteIncrement: 1      // Optional: set minute increment
            });

            $('.datepicker').each(function() {
                flatpickr(this, {
                    dateFormat: "Y-m-d",
                    minDate: "today"
                });
            });
        })
    </script>
    @endpush
@endif

@if($hasPlugin('guardImage'))
    @push('scripts')
    <script>
        function showLink(input, linkId, oldLinkId) {
            const linkDiv = document.getElementById(linkId);
            const oldLinkDiv = document.getElementById(oldLinkId);

            if (input.files.length > 0) {
                const file = input.files[0];
                const fileUrl = URL.createObjectURL(file); // Create a URL for the file
                linkDiv.querySelector('a').href = fileUrl; // Set the href of the link to the file URL
                linkDiv.style.display = 'block'; // Show the link

                if (oldLinkDiv) {
                    oldLinkDiv.style.display = 'none'; // Hide the old document link
                }
            } else {
                linkDiv.style.display = 'none'; // Hide the link if no file is selected
                if (oldLinkDiv) {
                    oldLinkDiv.style.display = 'block'; // Show the old document link if no new file is selected
                }
            }
        }
        </script>
    @endpush
@endif

@if($hasPlugin('contentEditor'))
    @push('styles')
        <link href="{{ asset('assets/css/summernote/summernote-lite.min.css') }}" rel="stylesheet">
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/js/summernote/summernote-lite.min.js') }}"></script>
        <script>
            $(function(){
                $('#answer').summernote({
                    height: 400,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['table', ['table']],
                        ['insert', ['link', 'hr']],
                        ['view', ['fullscreen', 'codeview', 'help']],
                    ],
                });
            })
        </script>
    @endpush
@endif

@if($hasPlugin('dataTable'))
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" type="text/css" />
        <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" type="text/css" />
        <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" type="text/css" />
    @endpush

    @push('scripts')
        <!-- Responsive examples -->
        <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
        <!-- Datatable init js -->
        <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
        <!-- Required datatable js -->
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <!-- Buttons examples -->
        <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    @endpush
@endif

@if($hasPlugin('jQueryValidate'))
    @push('scripts')
        <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    @endpush
@endif

@if($hasPlugin('chosen'))
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    @endpush
    @push('scripts')
        <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
    @endpush
@endif