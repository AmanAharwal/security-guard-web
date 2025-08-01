@extends('layouts.app')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Update Client Site</h4>

                        <div class="page-title-right">
                            <a href="{{ route('client-sites.index') }}" class="btn btn-primary"><i
                                    class="bx bx-arrow-back"></i> Back to Client Sites</a>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />

                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('client-sites.update', $clientSite->id) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row mb-2">
                                    <legend>Basic Details</legend>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="client">Client<span class="text-danger">*</span></label>
                                            <select name="client" id="client" @class(['form-control', 'is-invalid' => $errors->has('client')])>
                                                <option value="" selected disabled>Select Client</option>
                                                @foreach ($clients as $client)
                                                    <option value="{{ $client->id }}"
                                                        data-client-code="{{ $client->client_code }}"
                                                        @selected(($clientSite->client_id ?? '') == $client->id)>
                                                        {{ $client->client_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('client')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="client_code"
                                                value="{{ $clientSite->client->client_code ?? '' }}" label="Client Code"
                                                placeholder="Client Code" required="true" readonly />
                                            @error('client_code')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="location_code"
                                                value="{{ $clientSite->location_code ?? '' }}" label="Location Code"
                                                placeholder="Enter your Location Code" required="true" />
                                            @error('location_code')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="location" value="{{ $clientSite->location ?? '' }}"
                                                label="Location" placeholder="Enter your Location" required="true" />
                                            @error('location')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            @php $region_codes = config('clientSite.region_code'); @endphp
                                            <label for="region_code">Region Code<span class="text-danger">*</span></label>
                                            <select name="region_code" id="region_code" @class(['form-control', 'is-invalid' => $errors->has('region_code')])
                                                required>
                                                <option value="" selected disabled>Select Region Code</option>
                                                @foreach ($region_codes as $region_code)
                                                    <option value="{{ $region_code }}" @selected(isset($clientSite) && $clientSite->region_code == $region_code)>
                                                        {{ $region_code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('region_code')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            @php $regions = config('clientSite.region'); @endphp
                                            <label for="region">Region<span class="text-danger">*</span></label>
                                            <select name="region" id="region" @class(['form-control', 'is-invalid' => $errors->has('region')]) required>
                                                <option value="" selected disabled>Select Region</option>
                                                @foreach ($regions as $key => $region)
                                                    <option value="{{ $key }}" @selected(isset($clientSite) && $clientSite->region == $key)>
                                                        {{ $region }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('region')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="area_code">Area Code</label>
                                            <select name="area_code" id="area_code" @class(['form-control', 'is-invalid' => $errors->has('area_code')])>
                                                <option value="" selected disabled>Select Area Code</option>
                                                @if (isset($clientSite))
                                                    @foreach (config('clientSite.areas.' . $clientSite->region) as $area)
                                                        <option value="{{ $area['area_code'] }}"
                                                            @selected($clientSite->area_code == $area['area_code'])>
                                                            {{ $area['area_code'] }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('area_code')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="area">Area</label>
                                            <select name="area" id="area" @class(['form-control', 'is-invalid' => $errors->has('area')])>
                                                <option value="" selected disabled>Select Area</option>
                                                @if (isset($clientSite))
                                                    @foreach (config('clientSite.areas.' . $clientSite->region) as $area)
                                                        <option value="{{ $area['area'] }}" @selected($clientSite->area == $area['area'])>
                                                            {{ $area['area'] }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('area')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="latitude" value="{{ $clientSite->latitude ?? '' }}"
                                                label="Latitude" placeholder="Enter your Latitude" type="number"
                                                step="any" required="true" />
                                            @error('latitude')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="longitude" value="{{ $clientSite->longitude ?? '' }}"
                                                label="Longitude" placeholder="Enter your Longitude" type="number"
                                                step="any" required="true" />
                                            @error('longitude')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input type="number" name="radius"
                                                value="{{ $clientSite->radius ?? '' }}" label="Radius (in meters)"
                                                placeholder="Enter your Radius" min=0 step="any" required="true" />
                                            @error('radius')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <legend>Vanguard Supervison</legend>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="manager">Sr Manager</label>
                                            <select name="sr_manager_id" id="sr_manager" @class([
                                                'form-control',
                                                'is-invalid' => $errors->has('sr_manager_id'),
                                            ])>
                                                <option value="" selected disabled>Select Sr Manager</option>
                                                @foreach ($userManagers as $userManager)
                                                    <option value="{{ $userManager->id }}" @selected(($clientSite->sr_manager_id ?? '') == $userManager->id)>
                                                        {{ $userManager->first_name . ' ' . $userManager->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('sr_manager_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="sr_manager_email"
                                                value="{{ $clientSite->sr_manager_email ?? '' }}"
                                                label="Sr Manager Email" placeholder="Enter Sr Manager Email" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="manager">Manager<span class="text-danger">*</span></label>
                                            <select name="manager" id="manager" @class(['form-control', 'is-invalid' => $errors->has('manager')])>
                                                <option value="" selected disabled>Select Manager</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" @selected(($clientSite->manager_id ?? '') == $user->id)>
                                                        {{ $user->first_name . ' ' . $user->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('manager')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input type="manager_email" name="email"
                                                value="{{ $clientSite->manager_email ?? '' }}" label="Manager Email"
                                                placeholder="Enter Manager Email" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="supervisor" value="{{ $clientSite->supervisor ?? '' }}"
                                                label="Supervisor" placeholder="Enter supervisor" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="supervisor_email"
                                                value="{{ $clientSite->supervisor_email ?? '' }}"
                                                label="Supervisor Email" placeholder="Enter Supervisor Email" />
                                        </div>
                                    </div>
                                </div>
                                <legend>Invoicing</legend>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="service_status" class="form-label">Service Status</label>
                                            <select name="service_status" id="service_status" class="form-control">
                                                <option value="Active" @selected(($clientSite->status ?? '') === 'Active')>Active</option>
                                                <option value="Inactive" @selected(($clientSite->status ?? '') === 'Inactive')>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-4">
                                        <div class="mb-3">
                                            @php $rates = config('clientSite.rateMaster'); @endphp
                                            <label for="rates">Rate</label>
                                            <select name="rates" id="rates" @class(["form-control", "is-invalid" => $errors->has('rates')])>
                                                <option value="" selected disabled>Select Rate</option>
                                                @foreach ($rates as $key => $rate)
                                                    <option value="{{ $key }}" @selected(($clientSite->rate ?? '') === $key)>{{ $rate }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                </div>
                                <legend>Client Address</legend>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="client_address_unit_no"
                                                value="{{ $clientSite->unit_no_client ?? '' }}" label="Unit No"
                                                placeholder="Enter Unit No" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="client_address_building_name"
                                                value="{{ $clientSite->building_name_client ?? '' }}"
                                                label="Building Name" placeholder="Enter Building Name" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="client_address_street_no"
                                                value="{{ $clientSite->street_no_client ?? '' }}" label="Sreet No"
                                                placeholder="Enter Sreet No" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="client_address_street_road"
                                                value="{{ $clientSite->street_road_client ?? '' }}" label="Street Road"
                                                placeholder="Enter Street Road" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="client_address_parish"
                                                value="{{ $clientSite->parish_client ?? '' }}" label="Parish"
                                                placeholder="Enter your parish" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="client_address_country"
                                                value="{{ $clientSite->country_client ?? '' }}" label="Country"
                                                placeholder="Enter Country" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="client_address_postal_code"
                                                value="{{ $clientSite->postal_code_client ?? '' }}" label="Postal Code"
                                                placeholder="Enter Postal Code" />
                                        </div>
                                    </div>
                                </div>
                                <legend>Location Address</legend>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="location_address_unit_no"
                                                value="{{ $clientSite->unit_no_location ?? '' }}" label="Unit No"
                                                placeholder="Enter Unit No" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="location_address_building_name"
                                                value="{{ $clientSite->building_name_location ?? '' }}"
                                                label="Building Name" placeholder="Enter Building Name" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="location_address_street_no"
                                                value="{{ $clientSite->street_no_location ?? '' }}" label="Sreet No"
                                                placeholder="Enter Sreet No" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="location_address_street_road"
                                                value="{{ $clientSite->street_road_location ?? '' }}" label="Street Road"
                                                placeholder="Enter Street Road" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="location_address_parish"
                                                value="{{ $clientSite->parish_location ?? '' }}" label="Parish"
                                                placeholder="Enter your parish" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="location_address_country"
                                                value="{{ $clientSite->country_location ?? '' }}" label="Country"
                                                placeholder="Enter Country" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <x-form-input name="location_address_postal_code"
                                                value="{{ $clientSite->postal_code_location ?? '' }}" label="Postal Code"
                                                placeholder="Enter Postal Code" />
                                        </div>
                                    </div>
                                </div>

                                <div id="client-operation-contact-container">
                                    <legend>Client Operations Contact</legend>
                                    @foreach ($clientSite->clientOperation as $operationContact)
                                        <div class="row" id="client-operation-contact">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <x-form-input name="client_operation_name[]"
                                                        value="{{ $operationContact->name }}" label="Name"
                                                        placeholder="Enter name" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <x-form-input name="client_operation_position[]"
                                                        value="{{ $operationContact->position }}" label="Position"
                                                        placeholder="Enter position" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <x-form-input name="client_operation_email[]"
                                                        value="{{ $operationContact->email }}" label="Email"
                                                        placeholder="Enter email" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <x-form-input type="number" name="client_operation_telephone[]"
                                                        value="{{ $operationContact->telephone_number }}"
                                                        label="Telephone" placeholder="Enter telephone" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <x-form-input type="number" name="client_operation_mobile[]"
                                                        value="{{ $operationContact->mobile }}" label="Mobile"
                                                        placeholder="Enter mobile" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button"
                                                    class="btn btn-danger mt-4 remove-contact">Delete</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button type="button" class="btn btn-primary mt-4" id="add-operation-contact">Add
                                        more contact</button>
                                </div>

                                <div id="client-account-contact-container">
                                    <legend>Client Accounts Contact</legend>
                                    @foreach ($clientSite->clientAccount as $accountContact)
                                        <div class="row" id="client-account-contact">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <x-form-input name="client_account_name[]"
                                                        value="{{ $accountContact->name ?? '' }}" label="Name"
                                                        placeholder="Enter name" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <x-form-input name="client_account_position[]"
                                                        value="{{ $accountContact->position ?? '' }}" label="Position"
                                                        placeholder="Enter position" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <x-form-input name="client_account_email[]"
                                                        value="{{ $accountContact->email ?? '' }}" label="Email"
                                                        placeholder="Enter email" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <x-form-input type="number" name="client_account_telephone[]"
                                                        value="{{ $accountContact->telephone_number ?? '' }}"
                                                        label="Telephone" placeholder="Enter telephone" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <x-form-input type="number" name="client_account_mobile[]"
                                                        value="{{ $accountContact->mobile ?? '' }}" label="Mobile"
                                                        placeholder="Enter mobile" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button"
                                                    class="btn btn-danger remove-account mt-4">Delete</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-4 mb-2">
                                    <button type="button" class="btn btn-primary mt-4" id="add-account-contact">Add more
                                        contact</button>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-6 mb-2">
                                        <button type="submit" class="btn btn-primary w-md">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div> <!-- container-fluid -->
    </div>

    <x-include-plugins :plugins="['chosen']"></x-include-plugins>

    <script>
        $(function() {
            $('#region_code, #region, #area_code, #area').chosen({
                width: '100%',
                placeholder_text_single: 'Select an option'
            });

            $('#client').chosen({
                width: '100%',
                placeholder_text_multiple: 'Select Client'
            });

            $('#client').change(function() {
                let clientCode = $('#client option:selected').attr('data-client-code');
                $('#client_code').val(clientCode);
            });

            let updatingRegion = false;

            $('#region_code').change(function() {
                if (updatingRegion) return;

                const regionCode = $(this).val();

                if (regionCode) {
                    updatingRegion = true;

                    const regions = @json(config('clientSite.region'));
                    let regionKey = '';
                    for (const [key, value] of Object.entries(regions)) {
                        if (value === regionCode) {
                            regionKey = key;
                            break;
                        }
                    }

                    if (regionKey) {
                        $('#region').val(regionKey).trigger('chosen:updated');
                        updateAreas(regionKey);
                    }

                    updatingRegion = false;
                }
            });

            $('#region').change(function() {
                if (updatingRegion) return;

                const regionKey = $(this).val();

                if (regionKey) {
                    updatingRegion = true;

                    const regionName = @json(config('clientSite.region'))[regionKey];
                    const regionCode = @json(config('clientSite.region_reverse'))[regionName];

                    if (regionCode) {
                        $('#region_code').val(regionCode).trigger('chosen:updated');
                        updateAreas(regionKey);
                    }

                    updatingRegion = false;
                }
            });

            function updateAreas(regionKey) {
                const areas = @json(config('clientSite.areas'));
                const regionAreas = areas[regionKey] || [];

                const currentAreaCode = $('#area_code').val();
                const currentArea = $('#area').val();

                const areaCodeSelect = $('#area_code');
                areaCodeSelect.empty();
                areaCodeSelect.append('<option value="" selected disabled>Select Area Code</option>');

                const areaSelect = $('#area');
                areaSelect.empty();
                areaSelect.append('<option value="" selected disabled>Select Area</option>');

                regionAreas.forEach(area => {
                    areaCodeSelect.append(`<option value="${area.area_code}">${area.area_code}</option>`);
                    areaSelect.append(`<option value="${area.area}">${area.area}</option>`);
                });

                if (currentAreaCode && areaCodeSelect.find(`option[value="${currentAreaCode}"]`).length) {
                    areaCodeSelect.val(currentAreaCode);
                }

                if (currentArea && areaSelect.find(`option[value="${currentArea}"]`).length) {
                    areaSelect.val(currentArea);
                }

                areaCodeSelect.trigger('chosen:updated');
                areaSelect.trigger('chosen:updated');
            }

            $('#area_code').change(function() {
                const regionKey = $('#region').val();
                const areaCode = $(this).val();

                if (regionKey && areaCode) {
                    const areas = @json(config('clientSite.areas'));
                    const regionAreas = areas[regionKey] || [];

                    const selectedArea = regionAreas.find(area => area.area_code === areaCode);
                    if (selectedArea) {
                        $('#area').val(selectedArea.area).trigger('chosen:updated');
                    }
                }
            });

            $('#area').change(function() {
                const regionKey = $('#region').val();
                const areaName = $(this).val();

                if (regionKey && areaName) {
                    const areas = @json(config('clientSite.areas'));
                    const regionAreas = areas[regionKey] || [];

                    const selectedArea = regionAreas.find(area => area.area === areaName);
                    if (selectedArea) {
                        $('#area_code').val(selectedArea.area_code).trigger('chosen:updated');
                    }
                }
            });

            var newContactRow = `
                <div class="row" id="client-operation-contact">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <x-form-input name="client_operation_name[]" value="" label="Name" placeholder="Enter name"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <x-form-input name="client_operation_position[]" value="" label="Position" placeholder="Enter position"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <x-form-input name="client_operation_email[]" value="" label="Email" placeholder="Enter email"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <x-form-input type="number" name="client_operation_telephone[]" value="" label="Telephone" placeholder="Enter telephone"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <x-form-input type="number" name="client_operation_mobile[]" value="" label="Mobile" placeholder="Enter mobile"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-danger mt-4 remove-contact">Delete</button>
                    </div>
                </div>
            `;

            $('#add-operation-contact').click(function() {
                $('#client-operation-contact-container').append(newContactRow);
            });

            $('#client-operation-contact-container').on('click', '.remove-contact', function() {
                $(this).closest('.row').remove();
                rowCount = $('#client-operation-contact-container .row').length;
                if (rowCount == 0) {
                    $('#client-operation-contact-container').append(newContactRow);
                }
            });

            var newAccountRow = `
                 <div class="row" id="client-account-contact">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <x-form-input name="client_account_name[]" value="" label="Name" placeholder="Enter name"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <x-form-input name="client_account_position[]" value="" label="Position" placeholder="Enter position"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <x-form-input name="client_account_email[]" value="" label="Email" placeholder="Enter email"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <x-form-input type="number" name="client_account_telephone[]" value="" label="Telephone" placeholder="Enter telephone"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <x-form-input type="number" name="client_account_mobile[]" value="" label="Mobile" placeholder="Enter mobile"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-danger mt-4 remove-account">Delete</button>
                    </div>
                </div>
            `;
            $('#add-account-contact').click(function() {
                $('#client-account-contact-container').append(newAccountRow);
            });

            $('#client-account-contact-container').on('click', '.remove-account', function() {
                $(this).closest('.row').remove();
                rowCount = $('#client-account-contact-container .row').length;
                if (rowCount == 0) {
                    $('#client-account-contact-container').append(newAccountRow);
                }
            });
        });
    </script>
@endsection
