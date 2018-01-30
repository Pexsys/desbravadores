<div class="user-info">
    <?php
    $rootRelativeFile = "admin/fotos/F".fStrZero($_SESSION['USER']['ID_CAD_PESSOA'],5).".jpg";
    $logicalFile = PATTERNS::getVD()."$rootRelativeFile";
    $realPathFile = dirname(dirname(dirname(dirname(dirname(__DIR__)))))."/$rootRelativeFile";
    if (file_exists($realPathFile)){
        echo "<div class=\"image\"><img src=\"$logicalFile\" width=\"48\" height=\"48\" alt=\"User\" /></div>";
    } else {
        if (!is_null($_SESSION['USER']['TP_SEXO'])) {
            echo "<i class=\"fa ". ( $_SESSION['USER']['TP_SEXO'] == "F" ? "fa-female" : "fa-male" )." fa-fw\"></i>";
        }
    }
    ?>
    <div class="info-container">
        <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo titleCase($_SESSION['USER']['DS_USUARIO']);?></div>
        <?php echo (!is_null($_SESSION['USER']['EMAIL']) ? "<div class=\"email\">{$_SESSION['USER']['EMAIL']}</div>" : "");?>
        <div class="btn-group user-helper-dropdown">
            <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
            <ul class="dropdown-menu pull-right">
                <li><a href="javascript:void(0);"><i class="material-icons">person</i>Perfil</a></li>
                <li role="seperator" class="divider"></li>
                <li><a href="#" id="myBtnLogout"><i class="material-icons">input</i>Sair</a></li>
            </ul>
        </div>
    </div>
</div>
