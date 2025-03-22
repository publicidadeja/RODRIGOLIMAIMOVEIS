<div class="form-group">
    <label for="property_value_slider" class="control-label">{{ trans('plugins/real-estate::crm.form.property_value') }}</label>
    <div class="price-range-slider">
        <div id="property_value_slider"></div>
        <div class="range-values">
            <span id="property_value_min">R$ 0</span>
            <span id="property_value_max">R$ 10.000.000</span>
        </div>
        <input type="hidden" name="property_value_min" id="property_value_min_input" value="{{ request()->input('property_value_min', 0) }}">
        <input type="hidden" name="property_value_max" id="property_value_max_input" value="{{ request()->input('property_value_max', 10000000) }}">
    </div>
</div>