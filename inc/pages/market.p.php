<div class="page-content">
    <div class="row-fluid">
        <div class="span12">

            <div class="card  bg-purple text-white">
                <div class="card-body">
                    <p class="card-title typography-headline">
                        Vehicle Market
                    </p>
                </div>
            </div>
            <div class="card bg-dark-2 text-white">
                <div class="card-body">
                    <div class="card-text">
                        Total vehicles listed: <?php echo Config::$_PAGE_URL;?>
                    </div>
                </div>
            </div>
            <br>
            <div class="card  bg-purple text-white">
                <div class="card-body">
                    <p class="card-title typography-headline">
                        Listings
                    </p>
                </div>
            </div>
            <table class="table table-stripped table-dark table-responsive-sm">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th class="">Name</th>
                        <th class="">Total Listed</th>
                        <th>Average Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = Config::$g_con->prepare('SELECT * FROM `marketplace` ORDER BY `id` ASC LIMIT 95');
                    $q->execute();
                    while ($row = $q->fetch(PDO::FETCH_OBJ)) { ?>
                        <tr>
                            <td class="center"><img src="<?php echo Config::$_PAGE_URL ?>assets/img/vehicles/<?php echo $row->Model ?>.jpg" alt="560" title="560" style="width: 105px" /></td>
                            <td>
                                <a href="<?php echo Config::$_PAGE_URL ?>assets/img/vehicles/<?php echo $row->Model ?>.jpg">
                                <?php echo Config::$_vehicles[$car->Model] ?>
                                </a>
                                <br>
                                Top speed: <?php echo $row->MaxSpeed ?> km/h
                            </td>
                            <td><?php echo $row->List ?></td>
                            <td>$<?php echo number_format($row->Price, 0, '.', '.'); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>