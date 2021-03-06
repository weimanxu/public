/**
 * 资金管理通用js
 * @param         
 * @return 
 * @author OU.jc Create At 2017年8月10日
 */
$(function() {
    var clickFlag = true;

    placehoderChange();

    $('body').on('change', '.invest-type', function() { //动态切换信息
        placehoderChange();
    }).on('click', '#investBtn', function() { //弹出投资面板
        var login = $(this).data('login');
        if ($(this).hasClass('active')) {
            if (!!login) {
                $('#investModal').modal();
            } else {
                layer.tips('請先登錄！', '#investBtn', {
                    tips: [1, '#d9534f']
                });
            }
        }
    }).on('click', '.btn-invest', function() { //提交投资信息
        var id = tool.getParam('id');
        var type = $('option:selected').val();
        var num = $('input[name="invest_num"]').val();
        var fund_password = $('#fundPassword').val();
        var surplus = new BigNumber($('option:selected').data('target')).minus(Number($('option:selected').data('done'))).toNumber();

        if (!num) {
            layer.tips('請輸入投資金額', $('input[name="invest_num"]'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        if (num > surplus) {
            layer.tips('目前最多可投 ' + surplus + ' ' + type, $('input[name="invest_num"]'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        if (!fund_password) {
            layer.tips('請輸入資金密碼', $('#fundPassword'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        if (clickFlag) {
            clickFlag = false;
            $.post('/project/takeProjectInvestment', { type: type, project_id: id, amount: num, fund_password: fund_password }, function(res) {
                if (res.success) {
                    $('#investModal').modal('hide');
                    layer.msg(res.data, {
                        icon: 1,
                        time: 2000
                    }, function() {
                        window.location.reload();
                    });
                } else {
                    clickFlag = true;
                    layer.tips(res.error, '.btn-invest', {
                        tips: [1, '#d9534f']
                    });
                }
            }, 'json').error(function() {
                clickFlag = true;
                layer.tips('網絡出錯，請刷新重試！', '.btn-invest', {
                    tips: [1, '#d9534f']
                });
            });
        }
    });

    function placehoderChange() {
        var type = $('option:selected').val();
        var done = $('option:selected').data('done');
        var target = $('option:selected').data('target');
        var balance = $('option:selected').data('balance');
        var surplus = new BigNumber(target).minus(Number(done)).toNumber();
        $('input[name="invest_num"]').attr('placeholder', '最多可投 ' + (surplus < 0 ? 0 : surplus) + ' ' + type + ' / 您的餘額 ' + balance + ' ' + type);
    }
});