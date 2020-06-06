$(function() {
    function refresh (device_name, data) {
        $.each(data, function(item, value) {
            switch(item) {
                case 'power':
                    $('#'+device_name+'_'+item).prop('checked', value);
                    break;
                default:
                    $('#'+device_name+'_'+item).prop('value', value);
            }
            setState(device_name, '');
        });
    };
    function setState(device_name, state) {
        $('#'+device_name+'_state').text(state);
    }
    function sendCommand(device_name, command) {
        command['device_name']=device_name;
        $.getJSON('/api/command/',command, function(data) {
            if (data.error) {
                setState(device_name, data.error);
            } else {
                setState(device_name, '');
            }
        });
    }
    $('.device-state').each(function() {
        device_name=$(this).attr('device_name');
        $.getJSON('/api/command/',{'device_name': device_name}, function(data) {
            if(data.error) {
               return;
            }
            refresh(device_name, data);
        });
    });
    $('.device-state').click(function() {
        device_name=$(this).attr('device_name');
        $.getJSON('/api/command/',{'device_name': device_name}, function(data) {
            if(data.error) {
                setState(device_name, data.error);
                return;
            }
            refresh(device_name, data);
        });
    });
    $('.device-power').change(function() {
        on=$(this).is(':checked');
        device_name=$(this).attr('device_name');
        sendCommand(device_name, {'power': on});
    });
    $('.device-bright').change(function() {
        bright=$(this).val();
        device_name=$(this).attr('device_name');
        sendCommand(device_name, {'bright': bright});
    });
    $('.device-ct').change(function() {
        ct=$(this).val();
        device_name=$(this).attr('device_name');
        sendCommand(device_name, {'ct': ct});
    });
});
