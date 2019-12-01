<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    ->add_blueprints(Model_Blueprint::factory()->id('ktc_burgerjoint')->name('Fastfood-Küche'), true)

    // ++ STACK -> All blueprints below require local facilities, use 15 energy and require Rudolph to be present
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->requires_room('rudolph_upgrader')->category(
        'Unmenschliche Forschung'
    )->confirm('Rudolph muss für die OP betäubt werden; je mehr Operationen du vornimmst, desto länger wird die Betäubung dauern. Möchtest du fortfahren?')
    ->energy(15)->condition( function(Model_Player $p) {
        foreach (Tool_Scripts::at_location($p->location_class(), false, true) as $npc)
            if (Tool_System::instance_of($npc,Model_NPC_Event_RudolphBR::cls()))
                return (!$npc->get_status()->retrieve('passout') && !$npc->get_status()->retrieve('fragile')) ? true : 'Dein Rentier scheint gerade nicht für eine OP bereit zu sein ...';
        return '... und wen willst du operieren, wenn dein Rentier nicht hier ist?';
    } )->message('Du erklärst diese Operation als Erfolg! Rudolph würde sicher zustimmen, wenn die Betäubung schon abgeklungen wäre.')
    ->produces_advanced(function(Model_Player $p, bool $do) {
        if ($do) foreach (Tool_Scripts::at_location($p->location_class(), false, true) as $npc)
            if (Tool_System::instance_of($npc,Model_NPC_Event_RudolphBR::cls())) {

                /** @var Model_NPC_Event_RudolphBR $npc */
                $ctr = 	$npc->install_upgrade_getup_counter();
                switch ($ctr) {
                    case 0:case 1:case 2:case 3: $w = 3    * pow(2,$ctr); break;
                    case 4:case 5:               $w = 2    * pow(2,$ctr); break;
                    case 6:case 7:case 8:        $w = 1    * pow(2,$ctr+1); break;
                    default:                     $w = 0.75 * pow(2,$ctr+1); break;
                }

                new Model_Buffs_Befuddled($npc, $w);
            }
        return [];
    })
    ;})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:r_laser')
            ->name('Nasaler Ionenkonzentrator')
            ->description('Wird dieser kleine Schaltkreis in Rudolphs Riechkolben eingesetzt, kann er seine leuchtende Nase einmal pro Kampf als eine mächtige Waffe einsetzen.')
            //->material([Model_Items_Body::cls() => 1, Model_Items_Generic_Supercharger::cls() => 2])
            ->produces_advanced(function(Model_Player $p, bool $do) {
                if ($do) foreach (Tool_Scripts::at_location($p->location_class(), false, true) as $npc)
                    if (Tool_System::instance_of($npc,Model_NPC_Event_RudolphBR::cls())) {
                        /** @var Model_NPC_Event_RudolphBR $npc */
                        $npc->set_upgrade_provide_laser(true);
                        return [];
                    }
                return [];
            })
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:r_laser2')
            ->requires('i:r_laser')
            ->name('Nasaler Kernspin-Generator')
            ->description('Diese Erweiterung für den Nasalen Ionenkonzentrator ermöglicht es Rudolph, seinen Nasenlaser auch nüchtern zu verwenden (unter Verwendung von Energie, natürlich).')
            //->material([Model_Items_Body::cls() => 1, Model_Items_Generic_Supercharger::cls() => 2])
            ->produces_advanced(function(Model_Player $p, bool $do) {
                if ($do) foreach (Tool_Scripts::at_location($p->location_class(), false, true) as $npc)
                    if (Tool_System::instance_of($npc,Model_NPC_Event_RudolphBR::cls())) {
                        /** @var Model_NPC_Event_RudolphBR $npc */
                        $npc->set_upgrade_passive_laser(true);
                        return [];
                    }
                return [];
            })
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:r_laser3')
            ->requires('i:r_laser')
            ->name('Nasaler Rückkopplungs-Inebriator')
            ->description('An einen Ionenkonzentrator angeschlossen erzeugt dieser Schaltkreis eine direkt ans Gehirn gerichtete Rückkopplung. Damit verstärkt sich bei jedem Einsatz des Lasers zwar Rudolphs Trunkenheit, allerdings gibt es eine 50/50 Chance, dass der Nasenlaser erneut feuern kann.')
            //->material([Model_Items_Body::cls() => 1, Model_Items_Generic_Supercharger::cls() => 2])
            ->produces_advanced(function(Model_Player $p, bool $do) {
                if ($do) foreach (Tool_Scripts::at_location($p->location_class(), false, true) as $npc)
                    if (Tool_System::instance_of($npc,Model_NPC_Event_RudolphBR::cls())) {
                        /** @var Model_NPC_Event_RudolphBR $npc */
                        $npc->set_upgrade_reload_laser(true);
                        return [];
                    }
                    return [];
            })
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:r_eye')
            ->name('Retina-Navigator')
            ->description('Gewährt Rudolph eine moderate Chance, pro Tick 2x nach Items zu suchen. Mit etwas zusätzlichem Glück ist die zweite Suche unabhängig von der Fundrate der Ruine erfolgreich, und reduziert diese auch nicht weiter. Der Einsatz des Retina-Navigators erfordert höchste Konzentration, weshalb selbst geringe Mengen an Alkohol dessen Effektivität massiv reduzieren.')
            //->material([Model_Items_Body::cls() => 1, Model_Items_Generic_Supercharger::cls() => 2])
            ->produces_advanced(function(Model_Player $p, bool $do) {
                if ($do) foreach (Tool_Scripts::at_location($p->location_class(), false, true) as $npc)
                    if (Tool_System::instance_of($npc,Model_NPC_Event_RudolphBR::cls())) {
                        /** @var Model_NPC_Event_RudolphBR $npc */
                        $npc->set_upgrade_eye(true);
                        return [];
                    }
                return [];
            })
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:r_eye2')
            ->requires('i:r_eye')
            ->name('Retina-Radar')
            ->description('Diese Erweiterung des Retina-Navigators gewährt Rudolph eine geringe Chance, einen verschütteten Teil einer Ruine zu finden und damit die Item-Fundrate zu erhöhen. Wenn er jedoch Betrunken auf die Suche geht, richtet er möglicherweise eher Schaden an ...')
            //->material([Model_Items_Body::cls() => 1, Model_Items_Generic_Supercharger::cls() => 2])
            ->produces_advanced(function(Model_Player $p, bool $do) {
                if ($do) foreach (Tool_Scripts::at_location($p->location_class(), false, true) as $npc)
                    if (Tool_System::instance_of($npc,Model_NPC_Event_RudolphBR::cls())) {
                        /** @var Model_NPC_Event_RudolphBR $npc */
                        $npc->set_upgrade_eye2(true);
                        return [];
                    }
                return [];
            })
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:r_stomach')
            ->requires('i:r_laser')
            ->name('Kunstmagen')
            ->description('Ersetzt Rudolphs Magen durch eine künstliche Verarbeitungseinheit. Hat keinen eigenen Effekt, wird jedoch als Basis für weitere Erweiterungen benötigt.')
            //->material([Model_Items_Body::cls() => 1, Model_Items_Generic_Supercharger::cls() => 2])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:r_stomach2')
            ->requires('i:r_stomach')
            ->name('Ethanol-Thermoreaktor')
            ->description('Verbrennt automatisch Alkohol aus Rudolphs Magen, um Wärme zu erzeugen; Rudolphs Trunkenheit wird dadurch schneller reduziert. Der Effekt wird stärker, je mehr Rudolph getrunken hat.')
            //->material([Model_Items_Body::cls() => 1, Model_Items_Generic_Supercharger::cls() => 2])
            ->produces_advanced(function(Model_Player $p, bool $do) {
                if ($do) foreach (Tool_Scripts::at_location($p->location_class(), false, true) as $npc)
                    if (Tool_System::instance_of($npc,Model_NPC_Event_RudolphBR::cls())) {
                        /** @var Model_NPC_Event_RudolphBR $npc */
                        $npc->set_upgrade_thermo(true);
                        return [];
                    }
                return [];
            })
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:r_stomach3')
            ->requires('i:r_stomach')
            ->name('Auto-Fermentor')
            ->description('Wandelt Wasser und Nahrung aus Rudolphs Magen in Alkohol um, um ein einstellbares Trunkenheitsniveau zu halten. Kann bei Bedarf deaktiviert werden.')
            //->material([Model_Items_Body::cls() => 1, Model_Items_Generic_Supercharger::cls() => 2])
            ->produces_advanced(function(Model_Player $p, bool $do) {
                if ($do) foreach (Tool_Scripts::at_location($p->location_class(), false, true) as $npc)
                    if (Tool_System::instance_of($npc,Model_NPC_Event_RudolphBR::cls())) {
                        /** @var Model_NPC_Event_RudolphBR $npc */
                        $npc->set_upgrade_fermo(true);
                        return [];
                    }
                return [];
            })
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:r_electro')
            ->name('Elektrischer Neurostimulator')
            ->description('Wird an Rudolphs Zentralnervensystem angeschlossen und kann ihn, mithilfe einer Batterie, in einen künstlichen Trunkenheitszustand versetzen.')
            //->material([Model_Items_Body::cls() => 1, Model_Items_Generic_Supercharger::cls() => 2])
            ->produces_advanced(function(Model_Player $p, bool $do) {
                if ($do) foreach (Tool_Scripts::at_location($p->location_class(), false, true) as $npc)
                    if (Tool_System::instance_of($npc,Model_NPC_Event_RudolphBR::cls())) {
                        /** @var Model_NPC_Event_RudolphBR $npc */
                        $npc->set_upgrade_electro(true);
                        return [];
                    }
                return [];
            })
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:r_gyro')
            ->name('Gyro-Stabilisator')
            ->description('Dieses Gerät wird auf Rudolphs Rücken angebracht und verhindert, dass er im Trunkenheitszustand das Gleichgewicht verliert.')
            //->material([Model_Items_Body::cls() => 1, Model_Items_Generic_Supercharger::cls() => 2])
            ->produces_advanced(function(Model_Player $p, bool $do) {
                if ($do) foreach (Tool_Scripts::at_location($p->location_class(), false, true) as $npc)
                    if (Tool_System::instance_of($npc,Model_NPC_Event_RudolphBR::cls())) {
                        /** @var Model_NPC_Event_RudolphBR $npc */
                        $npc->set_upgrade_gyro(true);
                        return [];
                    }
                return [];
            })
    )

    ->pop_stack();
