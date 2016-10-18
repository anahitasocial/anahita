<? defined('KOOWA') or die; ?>

<h3 class="modal-header">
    <?= @text('Add Location') ?>
</h3>

<div class="modal-body">

    <div data-behavior="LocationSelector">
        <div id="location-form-container">
            <form action="<?= @route($locatable->getURL()) ?>" method="post">
                <input type="hidden" name="action" value="addlocation" />

                <div class="control-group">
                    <label class="label-group"  for="entity-name">
                    <?= @text('LIB-AN-ENTITY-NAME') ?>
                    </label>
                    <div class="controls">
                    <input required class="input-block-level" id="entity-name" size="30" maxlength="100" name="name" type="text" />
                    </div>
                </div>

                <div class="control-group">
                    <label class="label-group"  for="location-address">
                    <?= @text('COM-LOCATIONS-LOCATION-ADDRESS') ?>
                    </label>
                    <div class="controls">
                    <input class="input-block-level" id="location-address" size="30" maxlength="100" name="address" type="text" />
                    </div>
                </div>

                <div class="control-group">
                    <label class="label-group"  for="location-city">
                    <?= @text('COM-LOCATIONS-LOCATION-CITY') ?>
                    </label>
                    <div class="controls">
                    <input required class="input-block-level" id="location-city" size="30" maxlength="100" name="city" type="text" />
                    </div>
                </div>

                <div class="control-group">
                    <label class="label-group"  for="location-state-province">
                    <?= @text('COM-LOCATIONS-LOCATION-STATE-PROVINCE') ?>
                    </label>
                    <div class="controls">
                    <input class="input-block-level" id="location-state-province" size="30" maxlength="100" name="state_province" type="text" />
                    </div>
                </div>

                <div class="control-group">
                    <label class="label-group"  for="location-province">
                    <?= @text('COM-LOCATIONS-LOCATION-COUNTRY') ?>
                    </label>
                    <div class="controls">
                    <?= @helper('selector.country', array(
                        'name' => 'country',
                        'id' => 'select-country',
                        'requred' => 'required'
                      )) ?>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <button class="btn" data-dismiss="modal">
                            <?= @text('LIB-AN-ACTION-CANCEL') ?>
                        </button>

                        <button class="btn btn-primary" type="submit">
                            <?= @text('LIB-AN-ACTION-ADD') ?>
                        </button>

                        <a class="btn btn-link" href="#" data-trigger="FormSelector">
                            <?= @text('LIB-AN-ACTION-SEARCH') ?>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div id="locations-container">
            <? $url = 'layout=list_selector&locatable_id='.$locatable->id; ?>
            <?= @helper('ui.filterbox', @route($url), array('placeholder' => @text('LIB-AN-SEARCH-PLACEHOLDER'))) ?>
            <div class="an-entities" data-url="<?= @route($url.'&limit=100') ?>"></div>
        </div>
    </div>
</div>
