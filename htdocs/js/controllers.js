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
        });
    };
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
               return;        
            }
            refresh(device_name, data);
        });
    });
    $('.device-power').change(function() {
        on=$(this).is(':checked');
        device_name=$(this).attr('device_name');
        $.getJSON('/api/command/',{'power': on, 'device_name': device_name}, function(data) {
            if(data.error) {
               $('#'+device_name+'_state').text(data.error);
            }            
        });
    });
    $('.device-bright').change(function() {
        bright=$(this).val();
        device_name=$(this).attr('device_name');
        $.getJSON('/api/command/',{'bright': bright, 'device_name': device_name}, function(data) {
            if(data.error) {
               $('#'+device_name+'_state').text(data.error);
            }
        });
    });
    $('.device-ct').change(function() {
        ct=$(this).val();
        device_name=$(this).attr('device_name');
        $.getJSON('/api/command/',{'ct': ct, 'device_name': device_name}, function(data) {
            if(data.error) {
               $('#'+device_name+'_state').text(data.error);
            }
        });
    });
});