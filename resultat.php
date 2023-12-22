<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <title>Résultat en ligne</title>
</head>

<body>
    <div style="padding-top:10px">
        <a href="index.php" class="bouton-nav" style="margin-right:5px">Retour</a>
    </div>

    <div class="mid">

        <?php
        require('../config.php');

        $requeteMatchs = "SELECT * FROM matchs";
        $resultatMatchs = $base->prepare($requeteMatchs);
        $resultatMatchs->execute();
        echo "<br>";
        echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>";
        echo "<table>";
        echo "<tr><th>Equipe 1</th><th>Equipe 2</th><th>Cote Equipe 1</th><th>Cote Match Nul</th><th>Cote Equipe 2</th></tr>";

        $CoteTotal = 1;

        while ($row = $resultatMatchs->fetch()) {

            $requeteEquipe1 = "SELECT * FROM equipe WHERE id = ?";
            $resultatEquipe1 = $base->prepare($requeteEquipe1);
            $resultatEquipe1->execute([$row["equipe_1_id"]]);
            $equipe1 = $resultatEquipe1->fetch();


            $requeteEquipe2 = "SELECT * FROM equipe WHERE id = ?";
            $resultatEquipe2 = $base->prepare($requeteEquipe2);
            $resultatEquipe2->execute([$row["equipe_2_id"]]);
            $equipe2 = $resultatEquipe2->fetch();

            echo "<tr>";
            echo "<td><img src='../image/" . $equipe1["url_image"] . "'></td>";
            echo "<td><img src='../image/" . $equipe2["url_image"] . "'></td>";


            $resultat = $row["resultat"];
            $choiUtil = $row["choix_utilisateur"];



            if ($resultat == 1) {
                echo "<td style='background-color:green'>" . $row["cote_victoire"] . "</td>";
            } elseif ($choiUtil == 1) {
                echo "<td style='background-color:red'>" . $row["cote_victoire"] . "</td>";
            } else {
                echo "<td>" . $row["cote_victoire"] . "</td>";
            }

            if ($resultat == 2) {
                echo "<td style='background-color:green'>" . $row["cote_nul"] . "</td>";
            } elseif ($choiUtil == 2) {
                echo "<td style='background-color:red'>" . $row["cote_nul"] . "</td>";
            } else {
                echo "<td>" . $row["cote_nul"] . "</td>";
            }

            if ($resultat == 3) {
                echo "<td style='background-color:green'>" . $row["cote_defaite"] . "</td>";
            } elseif ($choiUtil == 3) {
                echo "<td style='background-color:red'>" . $row["cote_defaite"] . "</td>";
            } else {
                echo "<td>" . $row["cote_defaite"] . "</td>";
            }

            $ResultatParDéfaut = 'matchNul';

            $requeteChoix = "SELECT choix_utilisateur FROM matchs WHERE id = ?";
            $resultatChoix = $base->prepare($requeteChoix);
            $resultatChoix->execute([$row["id"]]);
            $choixUtilisateurRow = $resultatChoix->fetch();
            if ($choixUtilisateurRow) {
                $ChoixPrésedent = $choixUtilisateurRow["choix_utilisateur"];
                if ($ChoixPrésedent == 1) {
                    $ResultatParDéfaut = 'equipe1Gagne';
                } elseif ($ChoixPrésedent == 3) {
                    $ResultatParDéfaut = 'equipe2Gagne';
                }
            }


            echo "</tr>";
        }

        echo "</table>";
        ?>
    </div>
</body>

</html>