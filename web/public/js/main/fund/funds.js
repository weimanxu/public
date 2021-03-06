/**
 * 资金管理通用js
 * @param         
 * @return 
 * @author OU.jc Create At 2017年7月26日
 */
$(function() {
    var clickFlag = true;
    var type = $('.user-founds-wrap').data('type');
    var maxWithdraw = Number($('.qty').data('qty'));
    $('body').on('click', '.recall', function() { //提现撤回
        var _this = $(this);
        var id = $(this).data('id');
        $.post('/fund/revocation', { id: id }, function(res) {
            if (res.success) {
                layer.msg(res.data, {
                    icon: 1,
                    time: 2000
                }, function() {
                    window.location.reload();
                });
            } else {
                layer.tips(res.error, _this, {
                    tips: [4, '#d9534f']
                });
            }
        }, 'json');
    }).on('click', '.btn-address', function() { //提现地址新增和修改
        var data = $('#addressForm').serialize() + '&type=' + type;
        $.post('/fund/refreshAddress', data, function(res) {
            if (res.success) {
                $('#addressModal').modal('hide');
                layer.msg(res.data, {
                    icon: 1,
                    time: 2000
                }, function() {
                    window.location.reload();
                });
            } else {
                layer.tips(res.error, '.btn-address', {
                    tips: [1, '#d9534f']
                });
            }
        }, 'json').error(function() {
            layer.tips('刷網絡出錯，請刷新重試！', '.btn-address', {
                tips: [1, '#d9534f']
            });
        });
    }).on('click', '.btn-withdrawal', function() { //确认提现
        var data = $('#withdrawalForm').serialize() + '&type=' + type;
        var count = Number($('#minAmount').val());
        var password = $('#fundPassword').val();
        var limit = parseFloat($('.footnote .qty').text());
        var minimum = Number($('#minAmount').data('minimum'));
        if (count > limit) {
            layer.tips('超過最大提幣數量' + limit, $('#minAmount'), {
                tips: [1, '#d9534f']
            });
            return false;
        } else if (count < minimum) {
            layer.tips('低於最少提幣數量' + minimum, $('#minAmount'), {
                tips: [1, '#d9534f']
            });
            return false;
        } else if (password == '') {
            layer.tips('密碼不能為空', $('#fundPassword'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        if (clickFlag) {
            clickFlag = false;
            $.post('/fund/takeWithdraw', data, function(res) {
                if (res.success) {
                    layer.msg(res.data, {
                        icon: 1,
                        time: 2000
                    }, function() {
                        window.location.reload();
                    });
                } else {
                    clickFlag = true;
                    layer.tips(res.error, '.btn-withdrawal', {
                        tips: [1, '#d9534f']
                    });
                }
            }, 'json').error(function() {
                clickFlag = true;
                layer.tips('刷網絡出錯，請刷新重試！', '.btn-withdrawal', {
                    tips: [1, '#d9534f']
                });
            });
        }
    }).on('input', '#withdrawFee', function() { //动态修改最大可提现数量
        var _thisVal = new BigNumber(maxWithdraw).minus(Number($(this).val())).toNumber();
        $('.qty').text(_thisVal + ' ' + type.toUpperCase());
    });
});