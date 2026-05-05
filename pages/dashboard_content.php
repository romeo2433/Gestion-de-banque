<h1>Dashboard</h1>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="row">

    <div class="col-lg-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo $total_clients; ?></h3>
                <p>Clients</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo $total_prets; ?></h3>
                <p>Prêts</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo number_format($montant_total_prets); ?></h3>
                <p>Montant total</p>
            </div>
        </div>
    </div>

</div>