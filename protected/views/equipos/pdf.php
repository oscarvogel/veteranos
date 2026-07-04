<?php
$categoriaNombre = '';
if(isset($equipo) && $equipo->Categoria)
    $categoriaNombre = strtoupper($equipo->Categoria->Nombre);

if(strpos($categoriaNombre, 'SUPER') !== false)
    $categoriaRotulo = 'S U P E R&nbsp;&nbsp;V E T E R A N O S';
elseif(strpos($categoriaNombre, 'SENIOR') !== false)
    $categoriaRotulo = 'S E N I O R';
else
    $categoriaRotulo = 'V E T E R A N O S';

$delegadoTitular = isset($equipo->Delegado) ? $equipo->Delegado : '';
$delegadoSuplente = isset($equipo->DelegadoSuplente) ? $equipo->DelegadoSuplente : '';
$runtimePath = Yii::getPathOfAlias('application.runtime');

if (!function_exists('listaBuenaFeLogo')) {
function listaBuenaFeLogo($name, $data) {
    $path = Yii::getPathOfAlias('application.runtime') . DIRECTORY_SEPARATOR . $name;
    $bytes = base64_decode($data);
    if ($bytes !== false && (!is_file($path) || md5_file($path) !== md5($bytes))) {
        @file_put_contents($path, $bytes);
    }
    return is_file($path) ? $path : '';
}
}

if (!function_exists('listaBuenaFeFechaNacimiento')) {
function listaBuenaFeFechaNacimiento($fecha) {
    $fecha = trim((string)$fecha);
    if ($fecha === '') {
        return '';
    }
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $fecha, $matches)) {
        return $matches[3] . '/' . $matches[2] . '/' . $matches[1];
    }
    return $fecha;
}
}

$logoUnidosData =
    '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAMCAgICAgMCAgIDAwMDBAYEBAQEBAgGBgUGCQgKCgkI' .
    'CQkKDA8MCgsOCwkJDRENDg8QEBEQCgwSExIQEw8QEBD/2wBDAQMDAwQDBAgEBAgQCwkLEBAQEBAQ' .
    'EBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBD/wAARCABmAKoDASIA' .
    'AhEBAxEB/8QAHQAAAgMBAQEBAQAAAAAAAAAAAAYFBwgEAwIBCf/EAEwQAAEDAgIEBgwLBgUFAAAA' .
    'AAECAwQABQYRBxIhMRMYQVFx0wgUFSJVVmGBkZShpTI3RlJidoWxtMHEFiMzQkTRQ1NygpMkdJKy' .
    '4f/EABoBAAIDAQEAAAAAAAAAAAAAAAADAQQFAgb/xAAyEQABAwIEAwUIAwEBAAAAAAABAAIDBBEF' .
    'EiExQZGhEyJRsfAUQmFxgcHR8RUkQ1Ph/9oADAMBAAIRAxEAPwD+ielbTVhXQ/3L/aaBdZPdfh+A' .
    '7RabXq8Fqa2truIy/iJyyz3HdSBx1dFngDFXqsbr6VOzm+RP2l+mrK1CFtTjq6LPAGKvVY3X0cdX' .
    'RZ4AxV6rG6+ssYEsMa4qkT58dLzLeTaErGYKjtJ8w++ubHLdri3FqDbobTJab1nS2MsyrcPMPvq6' .
    'aJ7aYVLiADsOKpCuY6pNM0EkbngFrDjq6LPAGKvVY3X0cdXRZ4AxV6rG6+sV1Zlgw1Z27LFXPt7D' .
    'jq2+FcWtOZ27fYKijon1ry1htbxU1tayiYHPF7m2i0Rx1dFngDFXqsbr6OOros8AYq9VjdfWMZjj' .
    'Tst51hsNtKcUUIG5Kc9grxqoRY2VsG4utqcdXRZ4AxV6rG6+jjq6LPAGKvVY3X1iuioUranHV0We' .
    'AMVeqxuvo46uizwBir1WN19YuUy8hAdWytKFblFJAPQa+KLWRe62px1dFngDFXqsbr6OOros8AYq' .
    '9VjdfWK67LTaZd6mCFCCdcpKiVHJKQOU10xjnuDWi5K5e9sbS5xsAtkcdXRZ4AxV6rG6+jjq6LPA' .
    'GKvVY3X1j292KZYX22JjjKlOo108GrPZnlt2VG1L2OjcWPFiFEcjZWh7DcFbU46uizwBir1WN19H' .
    'HV0WeAMVeqxuvrFdFcLtbU46uizwBir1WN19HHV0WeAMVeqxuvrFdFCF/SPRfpQsGlmwSMR4ch3C' .
    'NGjTFwlomtoQsrShCyQELUMsnE8ueYOynCqA7Cr4rLr9YH/w0er/AKELKvZzfIn7S/TVlbInYBma' .
    '1T2c3yJ+0v01Ztwnb+6N+jNKTm22rhV9Cdv35UyKMyvEbdybJcsghYZHbAXViWaI1YLC029kkMNF' .
    '54/SIzV/bzVVc+Y7cJr0149+8srPkz5KsLH1wMWy9rJVk5Mc1P8AaNqvyHnqtq18ZkDXMpmbNHrp' .
    '5rHwWMua+pfu4+uvkuq2Q1XC4xoSR/GdSk9Ge32VZ2KZibdYJbjZ1SpAZb6VbPuzpR0eQOHurs5S' .
    'e9itnI/TVsHszrt0jz9kS2JVzvrGfmT+dd0f9Wgkn4u0Hl+Uut/tYhHBwbqfP8JIopthYED9mTc5' .
    'M9bTimS8G0ta2zLMDfnmfzrptGjwOx0yLxIcbUsZ8C1lmnpJ5fJVBmGVTyAG7i60H4pSsBJdsbcd' .
    '0k132JuI7eYbc4pDCnU6+tuy5j5M8q7sVYa/Z95pTLynY7+eqVDvkkbwct++v3DGFziHh3HJJYaZ' .
    'yGsEaxUo8lLZSzMqRDlu4HbqmPqoX0xmzWaRv080243uMCPY3YK1tqdf1UtNpyJTkc9bLkAqtKkL' .
    '9bWrRdHbe1KMgNBOaynIgkZ5eapPDuDJV5bEyU6Y0U/BOWa3Ogc3lqzVGfEanKG6jS3hb4qvSNgw' .
    '2lzF/dOt/n4BLlPujm36kWTclJ2vKDKD5BtPtI9FfNw0cM8AVWyY6HUjYh7LVV5wBlUtJUnC+Eih' .
    'CgHGWeDSed1W/wBpJ81W6Ggko5jNUCwaCVSrq+OshENOblxASLiq5d1L5JfSrNttXBN/6U7PvzNR' .
    'FdFvt8u6SkQ4TRcdWfMByknkFO0PRxDDQM+e844d4aASkHz5k1nR0tRiD3SMG534LSkqqfDmNjed' .
    'hoOKQaKZ8S4LXZ45nwn1Px0nJwLACkeXZvFLkdhyS+3HaGa3VhCR5ScqRNTyU7+zkGqsQVMVRH2k' .
    'ZuF50VZRwBh/guDykBeWXCB3l58t1V7cYL1tnPQH/hsrKSecch84p1VQTUYDpNj4JNJiENYS2Pce' .
    'K2Z2FXxWXX6wP/ho9X/VAdhV8Vl1+sD/AOGj1f8AVJXVlXs5vkT9pfpqo3RvDGU24KG3vWUn2n8q' .
    'vLs5vkT9pfpqqLATQbw62ob3HlqPpy/KtXBow+qBPAE/b7rKxqQspCBxIH3+yW9IUwv3lEQHNMZo' .
    'D/crafZlSvUpihxTuIbgpW8PqT5hsH3V9YZsy71dG2Ck8A2Q48rmSOTz7qr1Geqq3Bu5NlYp8lJS' .
    'NLtgL/dPeC7b3NsbanBquSjw68+Qfy+zb56SpjisT4q1WySh94No8jY5fQCadMZXcWqzqaZUEvSg' .
    'WWwP5U/zHzDZ56XtHVv4WbIuS096wjg0H6St/s++tiqY18kWHs2G/r5XP1WNSPcyObEH7nQevnYf' .
    'RN96ujFhti5ZSDqANst/OVyDo/tSvgqVd7ve5Fyly3VtNtkLBPeEq+CkDcMtp81cOP7oZd1FvbV+' .
    '6hjIjnWdp9GwU14Vhi24cYU03rOutmQoDepRGYHoyFNErquuyg9yPqf2k9k2joM5Hfk0+QP/AJ1K' .
    'W9ItwQ9Oj21tWfayStzyKVls9AHppiw9GRh/DKXpCdVQbVKeB35kZgejIVAYdwxPulzXeL8wtCOE' .
    'Lmo4MlOLz5vmimHGKJj1gfZhMLdW4pCVJQMzq57dnoqKZsl5a941IOUevkF1UujtFh7HaAjMfj6J' .
    '6Kv7ZGcxBf22nySZTxW6fo71eynzF16NhtaGoIDbz37pnIbG0gbSOjYBXLgzC7tqSblcEasl1Oqh' .
    'v/LSd+flPsrm0gWy4TVw34kZx9tCVIUG0lRSSdmwUmGGakoXygHO7nb1cps00NZXsiJ7jeV/Vgor' .
    'BMm6yr+gCa8pvVUt8KWVBScuXPykV36R7htiWtB3AvuD2J/OpfBuH3LLCW/LQEypOWsn5iRuT08p' .
    'qEueHbtiDFMlS2VsxULCS8sbNQAZavOagwzxUAiAJc88vxtqpE8EuIGYkBrBv4n776KWwLa2oFn7' .
    'ougB2XmsqPI2Nw9hNJ9+xJOu09T7chxphtR4BCVEBIG49NWY/DCbW7AhjVAjqZaGf0chVb2PClyu' .
    'FwSxKiOsMNK/fLWgp2DkGe8mpr4ZY44qSEacbcT61Rh88UkstXMdeF+A9aJ9fcVJwqt2Z8JyAVOZ' .
    '8+pnn6aV8BYfddkC9ymyG28xHBHwl/O6B99OkuTbYraWJr8dltwaiUOqAChuyyO8Vw4neuceyrNl' .
    'Z1l7Eq4MbUN5bSkCtCop2F7Z3m/ZjYbkrOp6h4Y6CMW7Q7nYBcMfE/bGL12ptzWi8GWU5bi6NpP3' .
    'iojSPAS3Ji3JCci8ktL8pTuPoPsqOwZa5kq+MSg0tLUVRccWQQM8tg6TU9pIebECHHJHCKeUsDyA' .
    'ZfnWcZH1WHySTeNx028lpNjZS4hHHD4WPXfzWlewq+Ky6/WB/wDDR6v+qA7Cr4rLr9YH/wANHq/6' .
    '86vRrKvZzfIn7S/TVTWj2e29anIGsOFjuFWrylKuX051cvZzfIn7S/TVluLLkwn0yYj62XUblJOR' .
    'q3Q1XscwktcbFU66l9shMd7HcKxL1giJd56rgiYuOp0gupCNYKPOOapGNFs+FbYohQZZT3y3FnNT' .
    'ivzPMBSMnHmJEo1O2WifnFlOdRE+6XC6OB2fLceUN2sdg6BuFarsTpInGWCPvnif2eiym4ZWTNEV' .
    'RJ3BwH6HVdF/vT19uKpiwUNgajSPmp/vympPD2MUWG3mELYHVFanNfhNXMndmMvJS1RWQyrmjlMz' .
    'T3jxWw+khkiELm90cF6ypDkuS7KeOa3llauknOmyxY8agW9uDcYjjnAJ1G1tEZlI3Ag0osMuSHm4' .
    '7IzW4oISM95O6vW4QZFsmOwZaQl1k5KAOY3Z76mnqJ6YmaM/AqKingqQIZB8R4qdvuOJ9zyZhJMR' .
    'lKgrYrNaiN2Z5vIKl4+kiMIyTLt7pkgZHg1AIUefbtFJzsaC3b2pAuHCSnT/AAEI2Np+krn8grzg' .
    'xu3ZrEPX1OHcS3rZZ5ZnLOrDcQq2SZg65dbwPy+AVd2H0b48pbYNv4j5/EqVn4vu064s3BKwyIyt' .
    'ZppPwRz58+dMSNJMLgdZy2Ph7LalKxq59O/2Ul3WD3MuUi38LwnAOFGtlln5q5a4bX1VO93e1J1v' .
    'rqunYfSVDG93QDS2mibYWkGci4Ovz2eEiu7Ayg5cFluKSd/lzr0vGkF2SwY9pjrj64yLrhBWB9ED' .
    'YOmk6ij+TqshZn3581P8ZS5w/Jty5Jlw5jN+zNGHLZVJj5lSe+79BO/IneKl5ekmMGiIVtdU5lsL' .
    'ygEj0b6Q6KI8TqYo+za7RRLhdLNJ2jm6/VdNxuMu6SlzJrpccX6AOYDkFTlgxvMtLKYcpntqOjYj' .
    'vsloHMDyjppaoqvFUywydox2vrdWZaWGaPsnt7vl8k/vaSYAbJYtshS+QLUkJz82dJt2u0y9TFTZ' .
    'ihrEaqUp+ChPMK4qKbUV89UMsjtEqmoKekOaJuq2p2FXxWXX6wP/AIaPV/1QHYVfFZdfrA/+Gj1f' .
    '9U1cWVezm+RP2l+mrK1ap7Ob5E/aX6asrUIRX00gOOobKtUKUEk82Zr5ooQrDmu2yy3Jm1mXb2YL' .
    'aEh2M5FK3HM95KgN56aiLLLw/Dm3RpmShguL1YUp5nXS2nmyI2eeuJvGdzQhsuRoT0hlOo3JcZzc' .
    'SOmuS34inQDJBQxJblnWebfb1krVz9NbL62IyNc3YX4HTS1t/K1t1jR0Mojc1+9hxGut77ed77Hx' .
    'U9NNygX21T3GbcsPqDaZEdHevAkZkjkVkd4r6u65l0xsILUOLIERXeodTkkp1QSVkbTlS9cMQz7g' .
    '5FUUssIhnNhplGqhBz35eauh7FlxcurV5QxGaktpKFFCDk6D87btrg1cJu25ylzT016+K6FJMLPs' .
    'Mwa4ddOnhtw0TC+WbpYbv21LgTFxE6zZjx9TgTzAkDMbKUbEM71A/wC5b/8AYV3PYtmuRJMJmDBj' .
    'sygQ4Gmsic95zz31Ew33o0pqTHTrOMrC0jLPaDnupNTURySRubrbfn8ddk6mp5Io5Gu0vtyA4ADd' .
    'PXdZUrGL1iftkVcV1ZQsFka6u9+EVVFCDFYw1fkNtoXwExKELKQVBIUBvoRiTFEhTj7FlaMhxOoJ' .
    'CIh4RKeYGvOytYutiH22LGuQ1K2uNyGs0k8+3Krrpmyu2Lr5tcuwI0CpNhdC3cNtl0zblp1P1Xo9' .
    'Ajv4csDam0IMiSULWEgKIJ56mJkm0QLqq2SpdubtzadRUTtVRcHe79YDfntzzqJucTGl3jR4z1lS' .
    '0iMoqb4JKUZc2zPZlXYXMckBw2CMZQRwYlFCC7l055Z12w5CcrD7uuU62FiLAjj9DxS3gPAzPb72' .
    'mYaXNwbkHYfUcFyWdcOFh28zWYrEngZKQyXmwrZmAknPpzyr9uaWr7Z7LPfZZZfkSjGcW0gJBTnl' .
    'urlbs+MI9tk2pNpUWZa0uOE6pVmOY515P23FptrFqXaHwzGcU6gpb77WPlFV80gj7Mxm2Xax3zX8' .
    'lYDWGTtGyC+a97jbLbzU65Obj4nbwq3aYnc/NLJQWQVKBTnra2+k67Q0w7lMjMBSmmHlIByzyGez' .
    'M0wKvuLWUpW7ZB2yhHBiWqIeFA6d1QrdxusSDMt6mVcHOUFPKcbOtmDnvpdXIyUWN9yRpawto3ny' .
    'TaON8RuLbAHW9zfV3Lmo2ijlyorLWqtqdhV8Vl1+sD/4aPV/1QHYVfFZdfrA/wDho9X/AEIWVezm' .
    '+RP2l+mrK1ap7Ob5E/aX6asrUIRRRRQhFfoBUQlIJJOQA3mvypbDFyh2m8NTZzZU0lKk5hOZQSNi' .
    'gK7iaHvDXGwPHwS5XOYwuaLkcPFdNtwTfbgAtxlMVs/zPHI/+O+mOFo5treRmzH5CuUIAQn8zXxc' .
    '9IkNoFFqirfWf53e9SPNvPspWuGKb7ccw/PWhB/w2u8T7N/nrZJw2k0AMh6fjzWMBidXqSI29fz5' .
    'J6VasG2cZyGYLZH+cvXV6CT91eLmM8LQRqxQV5cjDGQ9JyqtSSSSSSTvJopZxhzNIY2t+n6TBg7X' .
    '6zyOd9f2n1/SXHGYj2t5XlW6E/cDXC7pIuCv4NtjI/1KUr+1KFFIditW73+gT2YTRs9zqfymZzSD' .
    'flfATFR0NZ/ea8TjvEh/qmh0MppfopJr6o/6HmnigpR/mOSnjjfEp/rk/wDEn+1AxxiUf1yf+JP9' .
    'qgaK59tqf+h5lT7FTf8ANvIJhTj3Eyf6to9LIr1TpBv/APiJiuf6mv8A7SzRXQr6kf6HmuTh9Kf8' .
    'xyTBNxjJnxXY0i1W/wDepKdcNd8nyjy0v0UUmWaSc3kNynxQRwDLGLBbU7Cr4rLr9YH/AMNHq/6o' .
    'DsKvisuv1gf/AA0er/pSalTHWi7AmkrtL9tbF3R7ncL2t/1TzPB8Jq6/8Nac89RO/Pds5aVeK5oK' .
    '8RveczraKKEI4rmgrxG95zOto4rmgrxG95zOtoooQjiuaCvEb3nM62jiuaCvEb3nM62iihCOK5oK' .
    '8RveczraOK5oK8RveczraKKEI4rmgrxG95zOto4rmgrxG95zOtoooQjiuaCvEb3nM62jiuaCvEb3' .
    'nM62iihCOK5oK8RveczraOK5oK8RveczraKKEI4rmgrxG95zOto4rmgrxG95zOtoooQjiuaCvEb3' .
    'nM62jiuaCvEb3nM62iihCOK5oK8RveczraOK5oK8RveczraKKEJ1wVgLCeju1O2TB1q7nwn5CpTj' .
    'XDuu5ulKUlWbilHchIyzy2dNMFFFCF//2Q==';
$logoAsociacionData =
    '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAMCAgICAgMCAgIDAwMDBAYEBAQEBAgGBgUGCQgKCgkI' .
    'CQkKDA8MCgsOCwkJDRENDg8QEBEQCgwSExIQEw8QEBD/2wBDAQMDAwQDBAgEBAgQCwkLEBAQEBAQ' .
    'EBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBD/wAARCAB0AHIDASIA' .
    'AhEBAxEB/8QAHQAAAQUBAQEBAAAAAAAAAAAAAAIDBQYHBAgBCf/EADwQAAEDAwMCBAQEAwUJAAAA' .
    'AAECAwQABREGEiEHMRMiQVEUIzJhQlJxgQgVkRYkM4KiJTVDYnJzscLh/8QAGgEBAAMBAQEAAAAA' .
    'AAAAAAAAAAEDBAUCBv/EACcRAQACAgEEAQIHAAAAAAAAAAABAgMRBAUSIUHwceEGEyMkMaHB/9oA' .
    'DAMBAAIRAxEAPwD9UVkjGKTvV719c9KRQK3q96N6vek1x3i8W6w25663WSGYzIG5WCSok4SlKRyp' .
    'RJACRySQBQd25XvULcNaadt0o2925h+anvEhtLkvj9UNBSk/viqnfrtMkstStXvy7VCmH+52GIso' .
    'lSU7kjMh1HI+oZbQRjsSs8V1WSyaqmQWGbazF0lbhlXw0aKkOKBKgARnvtKck4O7tjGSE0nV1ze5' .
    'iaJva0/meVGY/wBK3QofuBQnVGoBgPaEuWTkhLUyGskD1x4oqPkdO9KR0GRqC5SHwpCUOLkyg2hY' .
    'HbPbnASCc5ISAeMgtjRvTm+MMw7dc0LEdlUdr4W5b1JQSokfUc/Wvvnv9hgJP+31njEJvbFxspJx' .
    'uuENTbQ/V4Za/wBVT8eUzLZRJiyG3mXBuQ42oKSoe4I4NV2fpe9MOpfsWoJCEYQ2uK8QWg0hGAhA' .
    'Awkkjknvn2GDT4avgbu/D8JWkL0FJ2yGGj/LrgonBK2Cdu3PG8FKjnO5PKaDVt6vejer3qv2DUzk' .
    '6a7YL3CFuvcZHiLj79zchrOPGYX+NGeD2Uk8KHYmeoFb1e9G9XvSaKBW9XvRSaKBbnpSKW56UigK' .
    'qNtaGsNXyrtK+Za9NSFQ4DR+hyaBh58j1KM+Gn2PiHvirfnHIGcc496qvSQZ6dWV858SU0uU6T3L' .
    'jrilrJ/zKNBNf2YsgvrmpFRAqa42hBWtRUlO3OFJSeArBwSOcAVjnUbrjc3prtl0M4liKyrY7cik' .
    'KU6rJBDQPATwRvOc4OMYzWmdWZ1xtvTPU860FSZbNsfU0pPdJ2HJH3Ayf2rwt0wmXebd7g9Ifcfi' .
    'qjD4lbiySFg/Kx9+4x7ZqnP3RSZrOnO6pbNj41r4bduo+RCz3zSsPUsxy4X+83yfIcUVFyRO8TGf' .
    'QbknA+1dtr0/bbTF+DtVtDQbIU4QSt0qxwpR7jv6YHNPyzcBCkqs7LK54bJjJeV5Sv8ApgnHYHjO' .
    'M1iDsq6KuTkl2RKFwU4S4vcoO7885xzmsmKuTkVnus+d4OLl9Wx2/MzzER88614/16Xs3VrXGg4o' .
    'mW+S/eYcRaVP2yUsuFbHZXgrOVIUnggcjGeK9E6fu+kOq2mbbqaAhEyE4pMhneMLZdSeUKHoQRgj' .
    'sfuK8h6benuWC2ybg4+ZqmiXFuk+JkLOM557YrcP4apL6bjqi3oSExSIkwpSnCQ+vxUqVgcZUG05' .
    '99uferePlmLTit5mPbf0jn5IzW4Oae6YmdW+nzw1XWWnnr5bkSbW4li82xfxVskfkeA+hXuhYyhQ' .
    '9QfcAh/TN9Y1LYIN9jtlpMtoKU0ru04DhaD90qCkn9KmapfTvLY1PDSkpZi6kmpaHoAsIdVj7b3F' .
    '1sfSLdRRRQFFFFAtz0pFLc9KrWttVuaZgsMWyEJ97ujnwtqg5x4z2MlSj+FtA8y1egHuRQR+udZ3' .
    'K3y4+jdFxmp2qrmgrZQ5yzAYzgyn8dkD8Ke61cD1rj6OTo1vg3Tp05fGLpO0rLUy4+2pOXWncuoU' .
    'Up4SQVKQR6FFRNm0jdHDPsFsvT4kzHfE1VqZA2vyX8cxYpP0BI8uRw2ngZWSRS9S6gtEm8Qunn8P' .
    'unHZV2tO+NNuMBwNRI0Zz/GbdeIPiKUQDu5IWAQScig9ETIke4RH4EtsOMSW1MuoPZSFDBH7g15J' .
    '1fom56Bvb1nmx/kPKK4stLYSmY2kHBJA5cSk4UDz6jg1qumuqt/0q5/JNfQ5kpEbYmTM8AJfiKUr' .
    'HzUJ8qm89nE8EdtxzjS2pmjte2ktNP229QJCQooylxJB7HHcH+hFVZcUZY1LD1DgU5+PstOpeKxr' .
    'bSB7X9rJ7fJdH/pU6lRbUX2VN/PG/wAZoD5oP4t4+rPvmtj1B/B/0lvD6pFtTdbMVnOyHJCmx+iX' .
    'ArH9a6rF/C9pGxw27enUV6djtkq2720lRPc52nH7YFZr8OIj9OXF5P4crFP2tp3739vu8/X6deG0' .
    'xLbpuC7Pvt1eDURlDXikAfUopPHsBnjuTwK9SdEenNx6eaTLWoZxmX66OCVcXd24JVjCWkn8qBxx' .
    'xknHerDprQ2kdEMLXZbYzGUU/NkurK3VD13OKJOPtkCq/rTrLp7TaXINnxeLoGlOJZZV8ptI7rcc' .
    '+lKf35II98aMOKMVde3X6b0+vAxRXxNvc/PS7Xe6wrHa5V4uLwaiw2VPOrPolIyf3qB6e22Zb9Mt' .
    'yLoyWrhdX37pLQe6HH1lzYf+lJSn/LWcWWFr7U16i3bVVyfegXF9p2BbJLQajOqZWFlxaBhQQlJK' .
    '20ElSykFXCRnWSrUMhfhBESIlCcqeyXvEVzwlPGB2yTz3A/NVzobSVFVm2MaqMidON6jzAnxWG2n' .
    'WS22VoVxgJPkHdJUdx4B+1TcCf8AFrfjvM+DJilKXmwrckFSQobVYG4YPsKG3XRRRRJbmOMnA9az' .
    'zRsaRq9+4dSFPBLtzK4FlUefhLche3egfndUFOE/9sdhWhuAEYIyDkEVmun7unpfGTorVhVDtTC1' .
    't2a9FJMYsqJKGX1dmXEZ25VhKgAQc5FBWdRzLz1N1BI6P9Opblp0zZMM6jvDB86lHkxGT6qPO9Xv' .
    'nP8AzXm2WWDoWztaI6X2CK06ykb1u58GPn/ivrHmcWe4QPMfdI5qsdAZNu05Z5XT66qRG1IzPlzX' .
    'wsj/AGk266pSJTKuzqCgoGU5xtwcVM3GfqXWeqbvozS91Tp622RbSLrcGUJVNfddbDgQwFeVsbVD' .
    'LpBOeEjjNBE6gtHTHQzyr91D1bJmammJCW5qpC0zu+Q3EYZ5bRn8KUkH8RVzVUsGktR6kvYuVt6b' .
    'rh2ZtaH2J9zkC0T31I+nLTCVJUCDjKm0HHrWu6V6baN0c6ubZ7OlVwd5euMpZkTHj6lTy8q/YED7' .
    'VZqDJ3bR1LYK/CumrYylDALfwU1tOAnkbnUnPlPdIB3qJGQkhUuP1MmuNFm8ataQ2wltTbdtgtlx' .
    'zYAVlSn+OcnGO5+wrVqKDJ3+nOqdTNRGdQlxxMRSwl66XEurcSpW7DjMYJSvBPHzB6DsBVq090z0' .
    '/ZExlyUCe7EA+HStpLceOR2LbKfKDnncrcvJPm5q3UKUlKStaglKRkknAA96CDu6npmorTa1MBLL' .
    'alXDxifrU2Cnw047EFaVHtkZHuKnBgnaDyO/2rIbL1W0JrvVT65IKHLGZMHwm2lyVuKLgSFhTKVA' .
    'oUBkDIII9fTTbW1ZZUXxrfBDbW8pPiRlsq3Dg5CwFe3JogqxAfy1DgJ2vuvSE57hLjqljP3woU0y' .
    'n4K+SnZR/wB4ltMdefRCOW/sclagPuefSk2F4GEVswHQh155wL+WArLiscBWewA7elN6inJahLa+' .
    'EfEtaSIZTtz45ISjCgfLyoZzxjOcih6TVFDeQ2kOkFe0binsT64ooktz0pp1pp9pbLzaHG3ElK0L' .
    'SFJUD3BB7inXPSkUGfah6F6AvzexiHKtCkrLiDbXy0htf5ktnKEK+6Ug/eqlL6F9Q4M5q66f6sOS' .
    'ZkdHhMyLnFIlJb9G1SGlAuIH5XErH2rbqKDLIcn+IqxgNTrTprUbafxtyyw8r9ylCf8ATU1G6hap' .
    'ZIRfulGoIqvVUNxmWn+qVCrzRQVlnqHp9Q/vse728+vxdrfbCf1VtKf6Gu1nWmkX/p1Hb0/Zx4Nn' .
    '+isVNZPuai5V0RIfbgW5Lcl5bqm3FqQVtMBIJUVEcE9hjPc+mDQdDF4tEohMa7QniewbkIV/4NUH' .
    'qd1V0vphKLfKs389Ko7kkhlTbiGlDyoSQScrUo4Awaa6wab1Zc9MtwrBGhSUPSm/jPBSIzqUA+UJ' .
    'JVhSSrAUCc857cVlNm6Na4uE2ZETppu2O29tTrbstKUtOvcYQ2tGckg/WMgY7iqMuS9Z7a125nN5' .
    'XIxXjHx8c2mffr6fP7/hd/4fel9401ppc+9XK0yGbxINzWY6C8qQHEDAWtQSEbDuxtGdxUc44rQ7' .
    '1Ft4jJuUJl5Zjy48jx3H1lKwh1J2IKye+MDHl571E9ONK3vSekIlq1HN/upe+IMVTY3xdwCiypaV' .
    'FKkBzccj0IHbNX9xtDiS262laTwUqGQf2q6NzHl0KbtWJtGpNxZMeWyHozrbiCSnLagoAg4IyOMg' .
    '5FQ998ORfdPwXclJkPSsehU20duf0Kgcfbt7OTCbFNcujTTyoEhK1zUtJ3+G4Ana4EDnBAUFYz+E' .
    '47mueVLRfr3GgWxSttofblS5IBASSglLKSR5ipKsqxwE4zyRUpmfSw0UUUei3PSkUtz0pFAUUUUB' .
    'UdOny2rlEtsJllSn23XlreUQAlG0YTjurKhx7A1I1y3KALhH2JX4T7Z8SO8By04Oyv09CPUEg96E' .
    'uZF4WsqhrtzypyfKthKTswc4V4hATsISee/pjPFckETtMWxhi4KZejMsgKU0QgoXkk43kbk47etD' .
    'cqbcWXJkCJIYlFJjrWFoU2l9tSklJSrukKz5hzinYT8KJ4sibGS28xgOysFwKWfq8+3jBOPYdvTA' .
    'PL6zu1LDakSGQ1AcO8Mk/MXtUQklQOEjgKwMntyKWh+6IeFrLa3VIUgmUFo/wiTgqB53eUjgYJ54' .
    'zwhbgt7r9y8X4GApPjyVyEADxMpAxzkZAOfvtx61xLSrVN0S5CuUmLCgtoUXowKFPvKKiUbzwpCd' .
    'qCQAQrdg+1B1z7Tbw+VG5fCKcZUXkbx85sLStSiCee2CfZRHFRitd29Ux232u4N3OU8wFxGm2zuL' .
    'mSClQA7YwvJx5cnsQak42j7E0pxyZGVcnXXC6p2efHUCSThII2oHJ4SAKl0MstEqbZQgkAEpSASA' .
    'MAfsKGpQzlnuV6ZSxqWQ0hhKQFxoDriEPLGMqUvhW3I4QPvkq9JK22u3WeN8HbIjcZncV7EDgqPc' .
    '/f8A+CuqiidCiiiiS3PSkU6UhXevmwe5oG6Kc2D3NGwe5oG6Kc2D3NGwe5oIJZcsbknxEuqtslTj' .
    '5dZQVORnFnKsgZJSSSoEDg8Hjmo2JcXr1ZU2qyMu7XtzKpjjBS0hjcQVAnAU5t9Bnz5z2NW/YPc1' .
    '8DaQMDgfaiNIT+ylpdIXcPiZ68bVGS+pSVjGAFIGEcDOPL6n3NTAASAlIwAMAewpzYPc0bB7miTd' .
    'FObB7mjYPc0DdFObB7mjYPc0DdFObB7migVRRRQFFFFAUUUUBRRRQFFFFAUUUUBRRRQFFFFB/9k=';
$logoUnidos = listaBuenaFeLogo('lista-buena-fe-unidos.jpg', $logoUnidosData);
$logoAsociacion = listaBuenaFeLogo('lista-buena-fe-asociacion.jpg', $logoAsociacionData);
?>
<style>
    @page { margin: 5mm 13mm 8mm 13mm; }
    body { font-family: Arial, sans-serif; font-size: 10pt; color: #111; }
    .header-box { border: 0.35mm solid #000; height: 10mm; margin: 0 2mm 1mm 2mm; }
    .header-table { width: 100%; border-collapse: collapse; }
    .header-table td { border: none; vertical-align: middle; height: 10mm; }
    .header-left { width: 20mm; text-align: left; padding-left: 1.5mm; }
    .header-right { width: 18mm; text-align: right; padding-right: 1.5mm; }
    .header-left img { width: 14mm; }
    .header-right img { width: 11mm; }
    .header-title { text-align: center; font-family: serif; font-size: 8.5pt; line-height: 1; }
    .torneo { text-align: center; font-size: 11pt; font-weight: bold; margin: 0.5mm 0 0.5mm; }
    .meta { width: 100%; border-collapse: collapse; margin-bottom: 0.5mm; }
    .meta td { border: none; font-size: 9.6pt; font-weight: bold; vertical-align: bottom; white-space: nowrap; }
    .meta .club { width: 34%; }
    .meta .vs { width: 31%; text-align: left; }
    .meta .fecha { width: 18%; text-align: right; }
    .meta .resultado { width: 17%; text-align: right; }
    .category { text-align: center; font-size: 11pt; font-weight: bold; margin: 0 0 1mm; }
    table.players { width: 100%; border-collapse: collapse; font-size: 9.8pt; line-height: 1.24; }
    table.players th, table.players td { border: 0.28mm solid #000; padding: 1.3mm 1mm; vertical-align: middle; }
    table.players th { text-align: center; font-weight: bold; }
    .num { width: 4.5%; text-align: center; }
    .ing { width: 4.5%; text-align: center; }
    .name { width: 33%; }
    .birthdate { width: 10.5%; text-align: center; }
    .dni { width: 15%; text-align: right; }
    .signature { width: 16%; }
    .short { width: 6%; text-align: center; }
    .footer { width: 100%; border-collapse: collapse; margin-top: 5mm; font-size: 9.8pt; }
    .footer td { border: none; padding: 0.55mm 0.8mm; vertical-align: bottom; }
    .footer .right { text-align: left; padding-left: 25mm; }
</style>

<div class="header-box">
    <table class="header-table">
        <tr>
            <td class="header-left"><?php if($logoUnidos): ?><img src="<?php echo $logoUnidos; ?>" /><?php endif; ?></td>
            <td class="header-title">
                <strong>ASOCIACI&Oacute;N CIVIL F&Uacute;TBOL<br />DE VETERANOS LDOR. GRAL. SAN MART&Iacute;N</strong>
            </td>
            <td class="header-right"><?php if($logoAsociacion): ?><img src="<?php echo $logoAsociacion; ?>" /><?php endif; ?></td>
        </tr>
    </table>
</div>

<div class="torneo"><?php echo CHtml::encode(strtoupper($torneo->Nombre)); ?></div>
<table class="meta">
    <tr>
        <td class="club">CLUB:&nbsp; <?php echo CHtml::encode(strtoupper($equipo->Nombre)); ?></td>
        <td class="vs">Vs. ......................................</td>
        <td class="fecha">Fecha: ...../...../......</td>
        <td class="resultado">Resultado: ................</td>
    </tr>
</table>
<div class="category"><?php echo $categoriaRotulo; ?></div>

<table class="players">
    <thead>
        <tr>
            <th class="num">N&deg;</th>
            <th class="ing">Ing.</th>
            <th class="name">Apellido y Nombres</th>
            <th class="birthdate">Fecha Nac.</th>
            <th class="dni">N&deg; Documento</th>
            <th class="signature">Firma</th>
            <th class="short">Goles</th>
            <th class="short">Am</th>
            <th class="short">Exp</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $rowCount = 0;
        foreach($jugadores as $jugador) {
            $rowCount++;
        ?>
            <tr>
                <td class="num"></td>
                <td class="ing"></td>
                <td class="name"><?php echo CHtml::encode($jugador->Nombre); ?></td>
                <td class="birthdate"><?php echo CHtml::encode(listaBuenaFeFechaNacimiento($jugador->fecha_nacimiento)); ?></td>
                <td class="dni"><?php echo CHtml::encode($jugador->DNI); ?></td>
                <td class="signature"></td>
                <td class="short"></td>
                <td class="short"></td>
                <td class="short"></td>
            </tr>
        <?php }
        for($i = $rowCount; $i < 24; $i++) { ?>
            <tr>
                <td class="num"></td><td class="ing"></td><td class="name">&nbsp;</td><td class="birthdate"></td><td class="dni"></td><td class="signature"></td><td class="short"></td><td class="short"></td><td class="short"></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<table class="footer">
    <tr>
        <td>Delegado Titular: <?php echo CHtml::encode($delegadoTitular); ?></td>
        <td class="right">Suplente: <?php echo CHtml::encode($delegadoSuplente); ?></td>
    </tr>
    <tr>
        <td>D.T. ....................................................</td>
        <td class="right">Firma:........................................</td>
    </tr>
    <tr>
        <td>Ayudante de Campo: ......................................</td>
        <td class="right">Firma:........................................</td>
    </tr>
    <tr>
        <td>Capit&aacute;n: .................................................</td>
        <td class="right">Firma:........................................</td>
    </tr>
    <tr>
        <td colspan="2">Cambios:......................................................................................................................................</td>
    </tr>
    <tr>
        <td colspan="2">......................................................................................................................................................</td>
    </tr>
</table>
