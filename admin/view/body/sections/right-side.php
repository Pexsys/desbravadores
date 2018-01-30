<?php
$theme = array(
    array( "cl" =>  "red",          "ds" => "Red" ),
    array( "cl" =>  "pink",         "ds" => "Pink" ),
    array( "cl" =>  "purple",       "ds" => "Purple" ),
    array( "cl" =>  "deep-purple",  "ds" => "Deep Purple" ),
    array( "cl" =>  "indigo",       "ds" => "Indigo" ),
    array( "cl" =>  "blue",         "ds" => "Blue" ),
    array( "cl" =>  "light-blue",   "ds" => "Light Blue" ),
    array( "cl" =>  "cyan",         "ds" => "Cyan" ),
    array( "cl" =>  "teal",         "ds" => "Teal" ),
    array( "cl" =>  "green",        "ds" => "Green" ),
    array( "cl" =>  "light-green",  "ds" => "Light Green" ),
    array( "cl" =>  "lime",         "ds" => "Lime" ),
    array( "cl" =>  "yellow",       "ds" => "Yellow" ),
    array( "cl" =>  "amber",        "ds" => "Amber" ),
    array( "cl" =>  "orange",       "ds" => "Orange" ),
    array( "cl" =>  "deep-orange",  "ds" => "Deep Orange" ),
    array( "cl" =>  "brown",        "ds" => "Brown" ),
    array( "cl" =>  "grey",         "ds" => "Grey" ),
    array( "cl" =>  "blue-grey",    "ds" => "Blue Grey" ),
    array( "cl" =>  "black",        "ds" => "Black" )
);
?>
<aside id="rightsidebar" class="right-sidebar">
    <ul class="nav nav-tabs tab-nav-right" role="tablist">
        <li role="presentation" class="active"><a href="#skins" data-toggle="tab">CORES</a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active in active" id="skins">
            <ul class="demo-choose-skin">
                <?php
                foreach ($theme as $k => $th):
                    echo "
                    <li data-theme=\"{$th["cl"]}\"". ($_SESSION['USER']["CLASS"] == $th["cl"] ? " class=\"active\"" : "") .">
                        <div class=\"{$th["cl"]}\"></div>
                        <span>{$th["ds"]}</span>
                    </li>";
                endforeach;
                ?>
            </ul>
        </div>
    </div>
</aside>
