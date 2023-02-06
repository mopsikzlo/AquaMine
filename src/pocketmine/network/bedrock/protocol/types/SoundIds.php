<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

/**
 * List of all sound IDs, used by PlaySoundPacket and StopSoundPacket.
 */
interface SoundIds{

	public const AMBIENT_WEATHER_LIGHTNING_IMPACT = "ambient.weather.lightning.impact";
	public const AMBIENT_WEATHER_RAIN = "ambient.weather.rain";
	public const AMBIENT_WEATHER_THUNDER = "ambient.weather.thunder";
	public const ARMOR_EQUIP_CHAIN = "armor.equip_chain";
	public const ARMOR_EQUIP_DIAMOND = "armor.equip_diamond";
	public const ARMOR_EQUIP_GENERIC = "armor.equip_generic";
	public const ARMOR_EQUIP_GOLD = "armor.equip_gold";
	public const ARMOR_EQUIP_IRON = "armor.equip_iron";
	public const ARMOR_EQUIP_LEATHER = "armor.equip_leather";
	public const BEACON_ACTIVATE = "beacon.activate";
	public const BEACON_AMBIENT = "beacon.ambient";
	public const BEACON_DEACTIVATE = "beacon.deactivate";
	public const BEACON_POWER = "beacon.power";
	public const BLOCK_BAMBOO_BREAK = "block.bamboo.break";
	public const BLOCK_BAMBOO_FALL = "block.bamboo.fall";
	public const BLOCK_BAMBOO_HIT = "block.bamboo.hit";
	public const BLOCK_BAMBOO_PLACE = "block.bamboo.place";
	public const BLOCK_BAMBOO_STEP = "block.bamboo.step";
	public const BLOCK_BAMBOO_SAPLING_BREAK = "block.bamboo_sapling.break";
	public const BLOCK_BAMBOO_SAPLING_PLACE = "block.bamboo_sapling.place";
	public const BLOCK_BARREL_CLOSE = "block.barrel.close";
	public const BLOCK_BARREL_OPEN = "block.barrel.open";
	public const BLOCK_BELL_HIT = "block.bell.hit";
	public const BLOCK_BLASTFURNACE_FIRE_CRACKLE = "block.blastfurnace.fire_crackle";
	public const BLOCK_CAMPFIRE_CRACKLE = "block.campfire.crackle";
	public const BLOCK_CARTOGRAPHY_TABLE_USE = "block.cartography_table.use";
	public const BLOCK_CHORUSFLOWER_DEATH = "block.chorusflower.death";
	public const BLOCK_CHORUSFLOWER_GROW = "block.chorusflower.grow";
	public const BLOCK_COMPOSTER_EMPTY = "block.composter.empty";
	public const BLOCK_COMPOSTER_FILL = "block.composter.fill";
	public const BLOCK_COMPOSTER_FILL_SUCCESS = "block.composter.fill_success";
	public const BLOCK_COMPOSTER_READY = "block.composter.ready";
	public const BLOCK_END_PORTAL_SPAWN = "block.end_portal.spawn";
	public const BLOCK_END_PORTAL_FRAME_FILL = "block.end_portal_frame.fill";
	public const BLOCK_FALSE_PERMISSIONS = "block.false_permissions";
	public const BLOCK_GRINDSTONE_USE = "block.grindstone.use";
	public const BLOCK_ITEMFRAME_ADD_ITEM = "block.itemframe.add_item";
	public const BLOCK_ITEMFRAME_BREAK = "block.itemframe.break";
	public const BLOCK_ITEMFRAME_PLACE = "block.itemframe.place";
	public const BLOCK_ITEMFRAME_REMOVE_ITEM = "block.itemframe.remove_item";
	public const BLOCK_ITEMFRAME_ROTATE_ITEM = "block.itemframe.rotate_item";
	public const BLOCK_LANTERN_BREAK = "block.lantern.break";
	public const BLOCK_LANTERN_FALL = "block.lantern.fall";
	public const BLOCK_LANTERN_HIT = "block.lantern.hit";
	public const BLOCK_LANTERN_PLACE = "block.lantern.place";
	public const BLOCK_LANTERN_STEP = "block.lantern.step";
	public const BLOCK_LOOM_USE = "block.loom.use";
	public const BLOCK_SCAFFOLDING_BREAK = "block.scaffolding.break";
	public const BLOCK_SCAFFOLDING_CLIMB = "block.scaffolding.climb";
	public const BLOCK_SCAFFOLDING_FALL = "block.scaffolding.fall";
	public const BLOCK_SCAFFOLDING_HIT = "block.scaffolding.hit";
	public const BLOCK_SCAFFOLDING_PLACE = "block.scaffolding.place";
	public const BLOCK_SCAFFOLDING_STEP = "block.scaffolding.step";
	public const BLOCK_SMOKER_SMOKE = "block.smoker.smoke";
	public const BLOCK_STONECUTTER_USE = "block.stonecutter.use";
	public const BLOCK_SWEET_BERRY_BUSH_BREAK = "block.sweet_berry_bush.break";
	public const BLOCK_SWEET_BERRY_BUSH_HURT = "block.sweet_berry_bush.hurt";
	public const BLOCK_SWEET_BERRY_BUSH_PICK = "block.sweet_berry_bush.pick";
	public const BLOCK_SWEET_BERRY_BUSH_PLACE = "block.sweet_berry_bush.place";
	public const BLOCK_TURTLE_EGG_BREAK = "block.turtle_egg.break";
	public const BLOCK_TURTLE_EGG_CRACK = "block.turtle_egg.crack";
	public const BLOCK_TURTLE_EGG_DROP = "block.turtle_egg.drop";
	public const BOTTLE_DRAGONBREATH = "bottle.dragonbreath";
	public const BUBBLE_DOWN = "bubble.down";
	public const BUBBLE_DOWNINSIDE = "bubble.downinside";
	public const BUBBLE_POP = "bubble.pop";
	public const BUBBLE_UP = "bubble.up";
	public const BUBBLE_UPINSIDE = "bubble.upinside";
	public const BUCKET_EMPTY_FISH = "bucket.empty_fish";
	public const BUCKET_EMPTY_LAVA = "bucket.empty_lava";
	public const BUCKET_EMPTY_WATER = "bucket.empty_water";
	public const BUCKET_FILL_FISH = "bucket.fill_fish";
	public const BUCKET_FILL_LAVA = "bucket.fill_lava";
	public const BUCKET_FILL_WATER = "bucket.fill_water";
	public const CAMERA_TAKE_PICTURE = "camera.take_picture";
	public const CAULDRON_ADDDYE = "cauldron.adddye";
	public const CAULDRON_CLEANARMOR = "cauldron.cleanarmor";
	public const CAULDRON_CLEANBANNER = "cauldron.cleanbanner";
	public const CAULDRON_DYEARMOR = "cauldron.dyearmor";
	public const CAULDRON_EXPLODE = "cauldron.explode";
	public const CAULDRON_FILLPOTION = "cauldron.fillpotion";
	public const CAULDRON_FILLWATER = "cauldron.fillwater";
	public const CAULDRON_TAKEPOTION = "cauldron.takepotion";
	public const CAULDRON_TAKEWATER = "cauldron.takewater";
	public const CONDUIT_ACTIVATE = "conduit.activate";
	public const CONDUIT_AMBIENT = "conduit.ambient";
	public const CONDUIT_ATTACK = "conduit.attack";
	public const CONDUIT_DEACTIVATE = "conduit.deactivate";
	public const CONDUIT_SHORT = "conduit.short";
	public const CROSSBOW_LOADING_END = "crossbow.loading.end";
	public const CROSSBOW_LOADING_MIDDLE = "crossbow.loading.middle";
	public const CROSSBOW_LOADING_START = "crossbow.loading.start";
	public const CROSSBOW_QUICK_CHARGE_END = "crossbow.quick_charge.end";
	public const CROSSBOW_QUICK_CHARGE_MIDDLE = "crossbow.quick_charge.middle";
	public const CROSSBOW_QUICK_CHARGE_START = "crossbow.quick_charge.start";
	public const CROSSBOW_SHOOT = "crossbow.shoot";
	public const DAMAGE_FALLBIG = "damage.fallbig";
	public const DAMAGE_FALLSMALL = "damage.fallsmall";
	public const DIG_CLOTH = "dig.cloth";
	public const DIG_GRASS = "dig.grass";
	public const DIG_GRAVEL = "dig.gravel";
	public const DIG_SAND = "dig.sand";
	public const DIG_SNOW = "dig.snow";
	public const DIG_STONE = "dig.stone";
	public const DIG_WOOD = "dig.wood";
	public const ELYTRA_LOOP = "elytra.loop";
	public const ENTITY_ZOMBIE_CONVERTED_TO_DROWNED = "entity.zombie.converted_to_drowned";
	public const FALL_CLOTH = "fall.cloth";
	public const FALL_EGG = "fall.egg";
	public const FALL_GRASS = "fall.grass";
	public const FALL_GRAVEL = "fall.gravel";
	public const FALL_LADDER = "fall.ladder";
	public const FALL_SAND = "fall.sand";
	public const FALL_SLIME = "fall.slime";
	public const FALL_SNOW = "fall.snow";
	public const FALL_STONE = "fall.stone";
	public const FALL_WOOD = "fall.wood";
	public const FIRE_FIRE = "fire.fire";
	public const FIRE_IGNITE = "fire.ignite";
	public const FIREWORK_BLAST = "firework.blast";
	public const FIREWORK_LARGE_BLAST = "firework.large_blast";
	public const FIREWORK_LAUNCH = "firework.launch";
	public const FIREWORK_SHOOT = "firework.shoot";
	public const FIREWORK_TWINKLE = "firework.twinkle";
	public const FURNACE_LIT = "furnace.lit";
	public const GAME_PLAYER_ATTACK_NODAMAGE = "game.player.attack.nodamage";
	public const GAME_PLAYER_ATTACK_STRONG = "game.player.attack.strong";
	public const GAME_PLAYER_DIE = "game.player.die";
	public const GAME_PLAYER_HURT = "game.player.hurt";
	public const HIT_CLOTH = "hit.cloth";
	public const HIT_GRASS = "hit.grass";
	public const HIT_GRAVEL = "hit.gravel";
	public const HIT_LADDER = "hit.ladder";
	public const HIT_SAND = "hit.sand";
	public const HIT_SLIME = "hit.slime";
	public const HIT_SNOW = "hit.snow";
	public const HIT_STONE = "hit.stone";
	public const HIT_WOOD = "hit.wood";
	public const ITEM_BOOK_PAGE_TURN = "item.book.page_turn";
	public const ITEM_BOOK_PUT = "item.book.put";
	public const ITEM_SHIELD_BLOCK = "item.shield.block";
	public const ITEM_TRIDENT_HIT = "item.trident.hit";
	public const ITEM_TRIDENT_HIT_GROUND = "item.trident.hit_ground";
	public const ITEM_TRIDENT_RETURN = "item.trident.return";
	public const ITEM_TRIDENT_RIPTIDE_1 = "item.trident.riptide_1";
	public const ITEM_TRIDENT_RIPTIDE_2 = "item.trident.riptide_2";
	public const ITEM_TRIDENT_RIPTIDE_3 = "item.trident.riptide_3";
	public const ITEM_TRIDENT_THROW = "item.trident.throw";
	public const ITEM_TRIDENT_THUNDER = "item.trident.thunder";
	public const JUMP_CLOTH = "jump.cloth";
	public const JUMP_GRASS = "jump.grass";
	public const JUMP_GRAVEL = "jump.gravel";
	public const JUMP_SAND = "jump.sand";
	public const JUMP_SLIME = "jump.slime";
	public const JUMP_SNOW = "jump.snow";
	public const JUMP_STONE = "jump.stone";
	public const JUMP_WOOD = "jump.wood";
	public const LAND_CLOTH = "land.cloth";
	public const LAND_GRASS = "land.grass";
	public const LAND_GRAVEL = "land.gravel";
	public const LAND_SAND = "land.sand";
	public const LAND_SLIME = "land.slime";
	public const LAND_SNOW = "land.snow";
	public const LAND_STONE = "land.stone";
	public const LAND_WOOD = "land.wood";
	public const LEASHKNOT_BREAK = "leashknot.break";
	public const LEASHKNOT_PLACE = "leashknot.place";
	public const LIQUID_LAVA = "liquid.lava";
	public const LIQUID_LAVAPOP = "liquid.lavapop";
	public const LIQUID_WATER = "liquid.water";
	public const MINECART_BASE = "minecart.base";
	public const MINECART_INSIDE = "minecart.inside";
	public const MOB_AGENT_SPAWN = "mob.agent.spawn";
	public const MOB_ARMOR_STAND_BREAK = "mob.armor_stand.break";
	public const MOB_ARMOR_STAND_HIT = "mob.armor_stand.hit";
	public const MOB_ARMOR_STAND_LAND = "mob.armor_stand.land";
	public const MOB_ARMOR_STAND_PLACE = "mob.armor_stand.place";
	public const MOB_BAT_DEATH = "mob.bat.death";
	public const MOB_BAT_HURT = "mob.bat.hurt";
	public const MOB_BAT_IDLE = "mob.bat.idle";
	public const MOB_BAT_TAKEOFF = "mob.bat.takeoff";
	public const MOB_BLAZE_BREATHE = "mob.blaze.breathe";
	public const MOB_BLAZE_DEATH = "mob.blaze.death";
	public const MOB_BLAZE_HIT = "mob.blaze.hit";
	public const MOB_BLAZE_SHOOT = "mob.blaze.shoot";
	public const MOB_CAT_BEG = "mob.cat.beg";
	public const MOB_CAT_EAT = "mob.cat.eat";
	public const MOB_CAT_HISS = "mob.cat.hiss";
	public const MOB_CAT_HIT = "mob.cat.hit";
	public const MOB_CAT_MEOW = "mob.cat.meow";
	public const MOB_CAT_PURR = "mob.cat.purr";
	public const MOB_CAT_PURREOW = "mob.cat.purreow";
	public const MOB_CAT_STRAYMEOW = "mob.cat.straymeow";
	public const MOB_CHICKEN_HURT = "mob.chicken.hurt";
	public const MOB_CHICKEN_PLOP = "mob.chicken.plop";
	public const MOB_CHICKEN_SAY = "mob.chicken.say";
	public const MOB_CHICKEN_STEP = "mob.chicken.step";
	public const MOB_COW_HURT = "mob.cow.hurt";
	public const MOB_COW_MILK = "mob.cow.milk";
	public const MOB_COW_SAY = "mob.cow.say";
	public const MOB_COW_STEP = "mob.cow.step";
	public const MOB_CREEPER_DEATH = "mob.creeper.death";
	public const MOB_CREEPER_SAY = "mob.creeper.say";
	public const MOB_DOLPHIN_ATTACK = "mob.dolphin.attack";
	public const MOB_DOLPHIN_BLOWHOLE = "mob.dolphin.blowhole";
	public const MOB_DOLPHIN_DEATH = "mob.dolphin.death";
	public const MOB_DOLPHIN_EAT = "mob.dolphin.eat";
	public const MOB_DOLPHIN_HURT = "mob.dolphin.hurt";
	public const MOB_DOLPHIN_IDLE = "mob.dolphin.idle";
	public const MOB_DOLPHIN_IDLE_WATER = "mob.dolphin.idle_water";
	public const MOB_DOLPHIN_JUMP = "mob.dolphin.jump";
	public const MOB_DOLPHIN_PLAY = "mob.dolphin.play";
	public const MOB_DOLPHIN_SPLASH = "mob.dolphin.splash";
	public const MOB_DOLPHIN_SWIM = "mob.dolphin.swim";
	public const MOB_DROWNED_DEATH = "mob.drowned.death";
	public const MOB_DROWNED_DEATH_WATER = "mob.drowned.death_water";
	public const MOB_DROWNED_HURT = "mob.drowned.hurt";
	public const MOB_DROWNED_HURT_WATER = "mob.drowned.hurt_water";
	public const MOB_DROWNED_SAY = "mob.drowned.say";
	public const MOB_DROWNED_SAY_WATER = "mob.drowned.say_water";
	public const MOB_DROWNED_SHOOT = "mob.drowned.shoot";
	public const MOB_DROWNED_STEP = "mob.drowned.step";
	public const MOB_DROWNED_SWIM = "mob.drowned.swim";
	public const MOB_ELDERGUARDIAN_CURSE = "mob.elderguardian.curse";
	public const MOB_ELDERGUARDIAN_DEATH = "mob.elderguardian.death";
	public const MOB_ELDERGUARDIAN_HIT = "mob.elderguardian.hit";
	public const MOB_ELDERGUARDIAN_IDLE = "mob.elderguardian.idle";
	public const MOB_ENDERDRAGON_DEATH = "mob.enderdragon.death";
	public const MOB_ENDERDRAGON_FLAP = "mob.enderdragon.flap";
	public const MOB_ENDERDRAGON_GROWL = "mob.enderdragon.growl";
	public const MOB_ENDERDRAGON_HIT = "mob.enderdragon.hit";
	public const MOB_ENDERMEN_DEATH = "mob.endermen.death";
	public const MOB_ENDERMEN_HIT = "mob.endermen.hit";
	public const MOB_ENDERMEN_IDLE = "mob.endermen.idle";
	public const MOB_ENDERMEN_PORTAL = "mob.endermen.portal";
	public const MOB_ENDERMEN_SCREAM = "mob.endermen.scream";
	public const MOB_ENDERMEN_STARE = "mob.endermen.stare";
	public const MOB_ENDERMITE_HIT = "mob.endermite.hit";
	public const MOB_ENDERMITE_KILL = "mob.endermite.kill";
	public const MOB_ENDERMITE_SAY = "mob.endermite.say";
	public const MOB_ENDERMITE_STEP = "mob.endermite.step";
	public const MOB_EVOCATION_FANGS_ATTACK = "mob.evocation_fangs.attack";
	public const MOB_EVOCATION_ILLAGER_AMBIENT = "mob.evocation_illager.ambient";
	public const MOB_EVOCATION_ILLAGER_CAST_SPELL = "mob.evocation_illager.cast_spell";
	public const MOB_EVOCATION_ILLAGER_DEATH = "mob.evocation_illager.death";
	public const MOB_EVOCATION_ILLAGER_HURT = "mob.evocation_illager.hurt";
	public const MOB_EVOCATION_ILLAGER_PREPARE_ATTACK = "mob.evocation_illager.prepare_attack";
	public const MOB_EVOCATION_ILLAGER_PREPARE_SUMMON = "mob.evocation_illager.prepare_summon";
	public const MOB_EVOCATION_ILLAGER_PREPARE_WOLOLO = "mob.evocation_illager.prepare_wololo";
	public const MOB_FISH_FLOP = "mob.fish.flop";
	public const MOB_FISH_HURT = "mob.fish.hurt";
	public const MOB_FISH_STEP = "mob.fish.step";
	public const MOB_GHAST_AFFECTIONATE_SCREAM = "mob.ghast.affectionate_scream";
	public const MOB_GHAST_CHARGE = "mob.ghast.charge";
	public const MOB_GHAST_DEATH = "mob.ghast.death";
	public const MOB_GHAST_FIREBALL = "mob.ghast.fireball";
	public const MOB_GHAST_MOAN = "mob.ghast.moan";
	public const MOB_GHAST_SCREAM = "mob.ghast.scream";
	public const MOB_GUARDIAN_AMBIENT = "mob.guardian.ambient";
	public const MOB_GUARDIAN_ATTACK_LOOP = "mob.guardian.attack_loop";
	public const MOB_GUARDIAN_DEATH = "mob.guardian.death";
	public const MOB_GUARDIAN_FLOP = "mob.guardian.flop";
	public const MOB_GUARDIAN_HIT = "mob.guardian.hit";
	public const MOB_GUARDIAN_LAND_DEATH = "mob.guardian.land_death";
	public const MOB_GUARDIAN_LAND_HIT = "mob.guardian.land_hit";
	public const MOB_GUARDIAN_LAND_IDLE = "mob.guardian.land_idle";
	public const MOB_HORSE_ANGRY = "mob.horse.angry";
	public const MOB_HORSE_ARMOR = "mob.horse.armor";
	public const MOB_HORSE_BREATHE = "mob.horse.breathe";
	public const MOB_HORSE_DEATH = "mob.horse.death";
	public const MOB_HORSE_DONKEY_ANGRY = "mob.horse.donkey.angry";
	public const MOB_HORSE_DONKEY_DEATH = "mob.horse.donkey.death";
	public const MOB_HORSE_DONKEY_HIT = "mob.horse.donkey.hit";
	public const MOB_HORSE_DONKEY_IDLE = "mob.horse.donkey.idle";
	public const MOB_HORSE_EAT = "mob.horse.eat";
	public const MOB_HORSE_GALLOP = "mob.horse.gallop";
	public const MOB_HORSE_HIT = "mob.horse.hit";
	public const MOB_HORSE_IDLE = "mob.horse.idle";
	public const MOB_HORSE_JUMP = "mob.horse.jump";
	public const MOB_HORSE_LAND = "mob.horse.land";
	public const MOB_HORSE_LEATHER = "mob.horse.leather";
	public const MOB_HORSE_SKELETON_DEATH = "mob.horse.skeleton.death";
	public const MOB_HORSE_SKELETON_HIT = "mob.horse.skeleton.hit";
	public const MOB_HORSE_SKELETON_IDLE = "mob.horse.skeleton.idle";
	public const MOB_HORSE_SOFT = "mob.horse.soft";
	public const MOB_HORSE_WOOD = "mob.horse.wood";
	public const MOB_HORSE_ZOMBIE_DEATH = "mob.horse.zombie.death";
	public const MOB_HORSE_ZOMBIE_HIT = "mob.horse.zombie.hit";
	public const MOB_HORSE_ZOMBIE_IDLE = "mob.horse.zombie.idle";
	public const MOB_HUSK_AMBIENT = "mob.husk.ambient";
	public const MOB_HUSK_DEATH = "mob.husk.death";
	public const MOB_HUSK_HURT = "mob.husk.hurt";
	public const MOB_HUSK_STEP = "mob.husk.step";
	public const MOB_IRONGOLEM_DEATH = "mob.irongolem.death";
	public const MOB_IRONGOLEM_HIT = "mob.irongolem.hit";
	public const MOB_IRONGOLEM_THROW = "mob.irongolem.throw";
	public const MOB_IRONGOLEM_WALK = "mob.irongolem.walk";
	public const MOB_LLAMA_ANGRY = "mob.llama.angry";
	public const MOB_LLAMA_DEATH = "mob.llama.death";
	public const MOB_LLAMA_EAT = "mob.llama.eat";
	public const MOB_LLAMA_HURT = "mob.llama.hurt";
	public const MOB_LLAMA_IDLE = "mob.llama.idle";
	public const MOB_LLAMA_SPIT = "mob.llama.spit";
	public const MOB_LLAMA_STEP = "mob.llama.step";
	public const MOB_LLAMA_SWAG = "mob.llama.swag";
	public const MOB_MAGMACUBE_BIG = "mob.magmacube.big";
	public const MOB_MAGMACUBE_JUMP = "mob.magmacube.jump";
	public const MOB_MAGMACUBE_SMALL = "mob.magmacube.small";
	public const MOB_OCELOT_DEATH = "mob.ocelot.death";
	public const MOB_OCELOT_IDLE = "mob.ocelot.idle";
	public const MOB_PANDA_BITE = "mob.panda.bite";
	public const MOB_PANDA_CANT_BREED = "mob.panda.cant_breed";
	public const MOB_PANDA_DEATH = "mob.panda.death";
	public const MOB_PANDA_EAT = "mob.panda.eat";
	public const MOB_PANDA_HURT = "mob.panda.hurt";
	public const MOB_PANDA_IDLE = "mob.panda.idle";
	public const MOB_PANDA_IDLE_AGGRESSIVE = "mob.panda.idle.aggressive";
	public const MOB_PANDA_IDLE_WORRIED = "mob.panda.idle.worried";
	public const MOB_PANDA_PRESNEEZE = "mob.panda.presneeze";
	public const MOB_PANDA_SNEEZE = "mob.panda.sneeze";
	public const MOB_PANDA_STEP = "mob.panda.step";
	public const MOB_PANDA_BABY_IDLE = "mob.panda_baby.idle";
	public const MOB_PARROT_DEATH = "mob.parrot.death";
	public const MOB_PARROT_EAT = "mob.parrot.eat";
	public const MOB_PARROT_FLY = "mob.parrot.fly";
	public const MOB_PARROT_HURT = "mob.parrot.hurt";
	public const MOB_PARROT_IDLE = "mob.parrot.idle";
	public const MOB_PARROT_STEP = "mob.parrot.step";
	public const MOB_PHANTOM_BITE = "mob.phantom.bite";
	public const MOB_PHANTOM_DEATH = "mob.phantom.death";
	public const MOB_PHANTOM_HURT = "mob.phantom.hurt";
	public const MOB_PHANTOM_IDLE = "mob.phantom.idle";
	public const MOB_PHANTOM_SWOOP = "mob.phantom.swoop";
	public const MOB_PIG_BOOST = "mob.pig.boost";
	public const MOB_PIG_DEATH = "mob.pig.death";
	public const MOB_PIG_SAY = "mob.pig.say";
	public const MOB_PIG_STEP = "mob.pig.step";
	public const MOB_PILLAGER_DEATH = "mob.pillager.death";
	public const MOB_PILLAGER_HURT = "mob.pillager.hurt";
	public const MOB_PILLAGER_IDLE = "mob.pillager.idle";
	public const MOB_POLARBEAR_DEATH = "mob.polarbear.death";
	public const MOB_POLARBEAR_HURT = "mob.polarbear.hurt";
	public const MOB_POLARBEAR_IDLE = "mob.polarbear.idle";
	public const MOB_POLARBEAR_STEP = "mob.polarbear.step";
	public const MOB_POLARBEAR_WARNING = "mob.polarbear.warning";
	public const MOB_POLARBEAR_BABY_IDLE = "mob.polarbear_baby.idle";
	public const MOB_RABBIT_DEATH = "mob.rabbit.death";
	public const MOB_RABBIT_HOP = "mob.rabbit.hop";
	public const MOB_RABBIT_HURT = "mob.rabbit.hurt";
	public const MOB_RABBIT_IDLE = "mob.rabbit.idle";
	public const MOB_RAVAGER_AMBIENT = "mob.ravager.ambient";
	public const MOB_RAVAGER_BITE = "mob.ravager.bite";
	public const MOB_RAVAGER_DEATH = "mob.ravager.death";
	public const MOB_RAVAGER_HURT = "mob.ravager.hurt";
	public const MOB_RAVAGER_ROAR = "mob.ravager.roar";
	public const MOB_RAVAGER_STEP = "mob.ravager.step";
	public const MOB_RAVAGER_STUN = "mob.ravager.stun";
	public const MOB_SHEEP_SAY = "mob.sheep.say";
	public const MOB_SHEEP_SHEAR = "mob.sheep.shear";
	public const MOB_SHEEP_STEP = "mob.sheep.step";
	public const MOB_SHULKER_AMBIENT = "mob.shulker.ambient";
	public const MOB_SHULKER_BULLET_HIT = "mob.shulker.bullet.hit";
	public const MOB_SHULKER_CLOSE = "mob.shulker.close";
	public const MOB_SHULKER_CLOSE_HURT = "mob.shulker.close.hurt";
	public const MOB_SHULKER_DEATH = "mob.shulker.death";
	public const MOB_SHULKER_HURT = "mob.shulker.hurt";
	public const MOB_SHULKER_OPEN = "mob.shulker.open";
	public const MOB_SHULKER_SHOOT = "mob.shulker.shoot";
	public const MOB_SHULKER_TELEPORT = "mob.shulker.teleport";
	public const MOB_SILVERFISH_HIT = "mob.silverfish.hit";
	public const MOB_SILVERFISH_KILL = "mob.silverfish.kill";
	public const MOB_SILVERFISH_SAY = "mob.silverfish.say";
	public const MOB_SILVERFISH_STEP = "mob.silverfish.step";
	public const MOB_SKELETON_DEATH = "mob.skeleton.death";
	public const MOB_SKELETON_HURT = "mob.skeleton.hurt";
	public const MOB_SKELETON_SAY = "mob.skeleton.say";
	public const MOB_SKELETON_STEP = "mob.skeleton.step";
	public const MOB_SLIME_ATTACK = "mob.slime.attack";
	public const MOB_SLIME_BIG = "mob.slime.big";
	public const MOB_SLIME_DEATH = "mob.slime.death";
	public const MOB_SLIME_HURT = "mob.slime.hurt";
	public const MOB_SLIME_JUMP = "mob.slime.jump";
	public const MOB_SLIME_SMALL = "mob.slime.small";
	public const MOB_SLIME_SQUISH = "mob.slime.squish";
	public const MOB_SNOWGOLEM_DEATH = "mob.snowgolem.death";
	public const MOB_SNOWGOLEM_HURT = "mob.snowgolem.hurt";
	public const MOB_SNOWGOLEM_SHOOT = "mob.snowgolem.shoot";
	public const MOB_SPIDER_DEATH = "mob.spider.death";
	public const MOB_SPIDER_SAY = "mob.spider.say";
	public const MOB_SPIDER_STEP = "mob.spider.step";
	public const MOB_SQUID_AMBIENT = "mob.squid.ambient";
	public const MOB_SQUID_DEATH = "mob.squid.death";
	public const MOB_SQUID_HURT = "mob.squid.hurt";
	public const MOB_STRAY_AMBIENT = "mob.stray.ambient";
	public const MOB_STRAY_DEATH = "mob.stray.death";
	public const MOB_STRAY_HURT = "mob.stray.hurt";
	public const MOB_STRAY_STEP = "mob.stray.step";
	public const MOB_TURTLE_AMBIENT = "mob.turtle.ambient";
	public const MOB_TURTLE_DEATH = "mob.turtle.death";
	public const MOB_TURTLE_HURT = "mob.turtle.hurt";
	public const MOB_TURTLE_STEP = "mob.turtle.step";
	public const MOB_TURTLE_SWIM = "mob.turtle.swim";
	public const MOB_TURTLE_BABY_BORN = "mob.turtle_baby.born";
	public const MOB_TURTLE_BABY_DEATH = "mob.turtle_baby.death";
	public const MOB_TURTLE_BABY_HURT = "mob.turtle_baby.hurt";
	public const MOB_TURTLE_BABY_STEP = "mob.turtle_baby.step";
	public const MOB_VEX_AMBIENT = "mob.vex.ambient";
	public const MOB_VEX_CHARGE = "mob.vex.charge";
	public const MOB_VEX_DEATH = "mob.vex.death";
	public const MOB_VEX_HURT = "mob.vex.hurt";
	public const MOB_VILLAGER_DEATH = "mob.villager.death";
	public const MOB_VILLAGER_HAGGLE = "mob.villager.haggle";
	public const MOB_VILLAGER_HIT = "mob.villager.hit";
	public const MOB_VILLAGER_IDLE = "mob.villager.idle";
	public const MOB_VILLAGER_NO = "mob.villager.no";
	public const MOB_VILLAGER_YES = "mob.villager.yes";
	public const MOB_VINDICATOR_DEATH = "mob.vindicator.death";
	public const MOB_VINDICATOR_HURT = "mob.vindicator.hurt";
	public const MOB_VINDICATOR_IDLE = "mob.vindicator.idle";
	public const MOB_WANDERINGTRADER_DEATH = "mob.wanderingtrader.death";
	public const MOB_WANDERINGTRADER_DISAPPEARED = "mob.wanderingtrader.disappeared";
	public const MOB_WANDERINGTRADER_DRINK_MILK = "mob.wanderingtrader.drink_milk";
	public const MOB_WANDERINGTRADER_DRINK_POTION = "mob.wanderingtrader.drink_potion";
	public const MOB_WANDERINGTRADER_HAGGLE = "mob.wanderingtrader.haggle";
	public const MOB_WANDERINGTRADER_HURT = "mob.wanderingtrader.hurt";
	public const MOB_WANDERINGTRADER_IDLE = "mob.wanderingtrader.idle";
	public const MOB_WANDERINGTRADER_NO = "mob.wanderingtrader.no";
	public const MOB_WANDERINGTRADER_REAPPEARED = "mob.wanderingtrader.reappeared";
	public const MOB_WANDERINGTRADER_YES = "mob.wanderingtrader.yes";
	public const MOB_WITCH_AMBIENT = "mob.witch.ambient";
	public const MOB_WITCH_DEATH = "mob.witch.death";
	public const MOB_WITCH_DRINK = "mob.witch.drink";
	public const MOB_WITCH_HURT = "mob.witch.hurt";
	public const MOB_WITCH_THROW = "mob.witch.throw";
	public const MOB_WITHER_AMBIENT = "mob.wither.ambient";
	public const MOB_WITHER_BREAK_BLOCK = "mob.wither.break_block";
	public const MOB_WITHER_DEATH = "mob.wither.death";
	public const MOB_WITHER_HURT = "mob.wither.hurt";
	public const MOB_WITHER_SHOOT = "mob.wither.shoot";
	public const MOB_WITHER_SPAWN = "mob.wither.spawn";
	public const MOB_WOLF_BARK = "mob.wolf.bark";
	public const MOB_WOLF_DEATH = "mob.wolf.death";
	public const MOB_WOLF_GROWL = "mob.wolf.growl";
	public const MOB_WOLF_HURT = "mob.wolf.hurt";
	public const MOB_WOLF_PANTING = "mob.wolf.panting";
	public const MOB_WOLF_SHAKE = "mob.wolf.shake";
	public const MOB_WOLF_STEP = "mob.wolf.step";
	public const MOB_WOLF_WHINE = "mob.wolf.whine";
	public const MOB_ZOMBIE_DEATH = "mob.zombie.death";
	public const MOB_ZOMBIE_HURT = "mob.zombie.hurt";
	public const MOB_ZOMBIE_REMEDY = "mob.zombie.remedy";
	public const MOB_ZOMBIE_SAY = "mob.zombie.say";
	public const MOB_ZOMBIE_STEP = "mob.zombie.step";
	public const MOB_ZOMBIE_UNFECT = "mob.zombie.unfect";
	public const MOB_ZOMBIE_WOOD = "mob.zombie.wood";
	public const MOB_ZOMBIE_WOODBREAK = "mob.zombie.woodbreak";
	public const MOB_ZOMBIE_VILLAGER_DEATH = "mob.zombie_villager.death";
	public const MOB_ZOMBIE_VILLAGER_HURT = "mob.zombie_villager.hurt";
	public const MOB_ZOMBIE_VILLAGER_SAY = "mob.zombie_villager.say";
	public const MOB_ZOMBIEPIG_ZPIG = "mob.zombiepig.zpig";
	public const MOB_ZOMBIEPIG_ZPIGANGRY = "mob.zombiepig.zpigangry";
	public const MOB_ZOMBIEPIG_ZPIGDEATH = "mob.zombiepig.zpigdeath";
	public const MOB_ZOMBIEPIG_ZPIGHURT = "mob.zombiepig.zpighurt";
	public const MUSIC_GAME = "music.game";
	public const MUSIC_GAME_CREATIVE = "music.game.creative";
	public const MUSIC_GAME_CREDITS = "music.game.credits";
	public const MUSIC_GAME_END = "music.game.end";
	public const MUSIC_GAME_ENDBOSS = "music.game.endboss";
	public const MUSIC_GAME_NETHER = "music.game.nether";
	public const MUSIC_MENU = "music.menu";
	public const NOTE_BASS = "note.bass";
	public const NOTE_BASSATTACK = "note.bassattack";
	public const NOTE_BD = "note.bd";
	public const NOTE_HARP = "note.harp";
	public const NOTE_HAT = "note.hat";
	public const NOTE_PLING = "note.pling";
	public const NOTE_SNARE = "note.snare";
	public const PORTAL_PORTAL = "portal.portal";
	public const PORTAL_TRAVEL = "portal.travel";
	public const PORTAL_TRIGGER = "portal.trigger";
	public const RAID_HORN = "raid.horn";
	public const RANDOM_ANVIL_BREAK = "random.anvil_break";
	public const RANDOM_ANVIL_LAND = "random.anvil_land";
	public const RANDOM_ANVIL_USE = "random.anvil_use";
	public const RANDOM_BOW = "random.bow";
	public const RANDOM_BOWHIT = "random.bowhit";
	public const RANDOM_BREAK = "random.break";
	public const RANDOM_BURP = "random.burp";
	public const RANDOM_CHESTCLOSED = "random.chestclosed";
	public const RANDOM_CHESTOPEN = "random.chestopen";
	public const RANDOM_CLICK = "random.click";
	public const RANDOM_DOOR_CLOSE = "random.door_close";
	public const RANDOM_DOOR_OPEN = "random.door_open";
	public const RANDOM_DRINK = "random.drink";
	public const RANDOM_EAT = "random.eat";
	public const RANDOM_ENDERCHESTCLOSED = "random.enderchestclosed";
	public const RANDOM_ENDERCHESTOPEN = "random.enderchestopen";
	public const RANDOM_EXPLODE = "random.explode";
	public const RANDOM_FIZZ = "random.fizz";
	public const RANDOM_FUSE = "random.fuse";
	public const RANDOM_GLASS = "random.glass";
	public const RANDOM_HURT = "random.hurt";
	public const RANDOM_LEVELUP = "random.levelup";
	public const RANDOM_ORB = "random.orb";
	public const RANDOM_POP = "random.pop";
	public const RANDOM_POP2 = "random.pop2";
	public const RANDOM_POTION_BREWED = "random.potion.brewed";
	public const RANDOM_SCREENSHOT = "random.screenshot";
	public const RANDOM_SHULKERBOXCLOSED = "random.shulkerboxclosed";
	public const RANDOM_SHULKERBOXOPEN = "random.shulkerboxopen";
	public const RANDOM_SPLASH = "random.splash";
	public const RANDOM_SWIM = "random.swim";
	public const RANDOM_TOAST = "random.toast";
	public const RANDOM_TOTEM = "random.totem";
	public const RECORD_11 = "record.11";
	public const RECORD_13 = "record.13";
	public const RECORD_BLOCKS = "record.blocks";
	public const RECORD_CAT = "record.cat";
	public const RECORD_CHIRP = "record.chirp";
	public const RECORD_FAR = "record.far";
	public const RECORD_MALL = "record.mall";
	public const RECORD_MELLOHI = "record.mellohi";
	public const RECORD_STAL = "record.stal";
	public const RECORD_STRAD = "record.strad";
	public const RECORD_WAIT = "record.wait";
	public const RECORD_WARD = "record.ward";
	public const STEP_CLOTH = "step.cloth";
	public const STEP_GRASS = "step.grass";
	public const STEP_GRAVEL = "step.gravel";
	public const STEP_LADDER = "step.ladder";
	public const STEP_SAND = "step.sand";
	public const STEP_SLIME = "step.slime";
	public const STEP_SNOW = "step.snow";
	public const STEP_STONE = "step.stone";
	public const STEP_WOOD = "step.wood";
	public const TILE_PISTON_IN = "tile.piston.in";
	public const TILE_PISTON_OUT = "tile.piston.out";
	public const UI_CARTOGRAPHY_TABLE_TAKE_RESULT = "ui.cartography_table.take_result";
	public const UI_LOOM_SELECT_PATTERN = "ui.loom.select_pattern";
	public const UI_LOOM_TAKE_RESULT = "ui.loom.take_result";
	public const UI_STONECUTTER_TAKE_RESULT = "ui.stonecutter.take_result";
	public const USE_CLOTH = "use.cloth";
	public const USE_GRASS = "use.grass";
	public const USE_GRAVEL = "use.gravel";
	public const USE_LADDER = "use.ladder";
	public const USE_SAND = "use.sand";
	public const USE_SLIME = "use.slime";
	public const USE_SNOW = "use.snow";
	public const USE_STONE = "use.stone";
	public const USE_WOOD = "use.wood";
	public const VR_STUTTERTURN = "vr.stutterturn";

}