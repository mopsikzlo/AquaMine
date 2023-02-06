<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

/**
 * List of all sound IDs, used by PlaySoundPacket and StopSoundPacket.
 */
interface SoundIds{

	public const AMBIENT_WEATHER_LIGHTNING_IMPACT = "ambient.weather.lightning.impact";
	public const AMBIENT_WEATHER_RAIN = "ambient.weather.rain";
	public const AMBIENT_WEATHER_THUNDER = "ambient.weather.thunder";
	public const BLOCK_CHORUSFLOWER_DEATH = "block.chorusflower.death";
	public const BLOCK_CHORUSFLOWER_GROW = "block.chorusflower.grow";
	public const BLOCK_FALSE_PERMISSIONS = "block.false_permissions";
	public const BLOCK_ITEMFRAME_ADD_ITEM = "block.itemframe.add_item";
	public const BLOCK_ITEMFRAME_BREAK = "block.itemframe.break";
	public const BLOCK_ITEMFRAME_PLACE = "block.itemframe.place";
	public const BLOCK_ITEMFRAME_REMOVE_ITEM = "block.itemframe.remove_item";
	public const BLOCK_ITEMFRAME_ROTATE_ITEM = "block.itemframe.rotate_item";
	public const BUCKET_EMPTY_LAVA = "bucket.empty_lava";
	public const BUCKET_EMPTY_WATER = "bucket.empty_water";
	public const BUCKET_FILL_LAVA = "bucket.fill_lava";
	public const BUCKET_FILL_WATER = "bucket.fill_water";
	public const CAMERA_TAKE_PICTURE = "camera.take_picture";
	public const CAULDRON_ADDDYE = "cauldron.adddye";
	public const CAULDRON_CLEANARMOR = "cauldron.cleanarmor";
	public const CAULDRON_DYEARMOR = "cauldron.dyearmor";
	public const CAULDRON_EXPLODE = "cauldron.explode";
	public const CAULDRON_FILLPOTION = "cauldron.fillpotion";
	public const CAULDRON_FILLWATER = "cauldron.fillwater";
	public const CAULDRON_TAKEPOTION = "cauldron.takepotion";
	public const CAULDRON_TAKEWATER = "cauldron.takewater";
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
	public const FIRE_FIRE = "fire.fire";
	public const FIRE_IGNITE = "fire.ignite";
	public const GAME_PLAYER_ATTACK_NODAMAGE = "game.player.attack.nodamage";
	public const GAME_PLAYER_DIE = "game.player.die";
	public const GAME_PLAYER_HURT = "game.player.hurt";
	public const JUMP_CLOTH = "jump.cloth";
	public const JUMP_GRASS = "jump.grass";
	public const JUMP_GRAVEL = "jump.gravel";
	public const JUMP_SAND = "jump.sand";
	public const JUMP_SLIME = "jump.slime";
	public const JUMP_SNOW = "jump.snow";
	public const JUMP_STONE = "jump.stone";
	public const JUMP_WOOD = "jump.wood";
	public const LIQUID_LAVA = "liquid.lava";
	public const LIQUID_LAVAPOP = "liquid.lavapop";
	public const LIQUID_WATER = "liquid.water";
	public const MINECART_BASE = "minecart.base";
	public const MINECART_INSIDE = "minecart.inside";
	public const MOB_BAT_DEATH = "mob.bat.death";
	public const MOB_BAT_HURT = "mob.bat.hurt";
	public const MOB_BAT_IDLE = "mob.bat.idle";
	public const MOB_BAT_TAKEOFF = "mob.bat.takeoff";
	public const MOB_BLAZE_BREATHE = "mob.blaze.breathe";
	public const MOB_BLAZE_DEATH = "mob.blaze.death";
	public const MOB_BLAZE_HIT = "mob.blaze.hit";
	public const MOB_BLAZE_SHOOT = "mob.blaze.shoot";
	public const MOB_CAT_HISS = "mob.cat.hiss";
	public const MOB_CAT_HIT = "mob.cat.hit";
	public const MOB_CAT_MEOW = "mob.cat.meow";
	public const MOB_CAT_PURR = "mob.cat.purr";
	public const MOB_CAT_PURREOW = "mob.cat.purreow";
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
	public const MOB_PIG_BOOST = "mob.pig.boost";
	public const MOB_PIG_DEATH = "mob.pig.death";
	public const MOB_PIG_SAY = "mob.pig.say";
	public const MOB_PIG_STEP = "mob.pig.step";
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
	public const PORTAL_TRIGGER = "portal.trigger";
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
	public const RANDOM_EXPLODE = "random.explode";
	public const RANDOM_FIZZ = "random.fizz";
	public const RANDOM_FUSE = "random.fuse";
	public const RANDOM_GLASS = "random.glass";
	public const RANDOM_HURT = "random.hurt";
	public const RANDOM_LEVELUP = "random.levelup";
	public const RANDOM_ORB = "random.orb";
	public const RANDOM_POP = "random.pop";
	public const RANDOM_POP2 = "random.pop2";
	public const RANDOM_SHULKERBOXCLOSED = "random.shulkerboxclosed";
	public const RANDOM_SHULKERBOXOPEN = "random.shulkerboxopen";
	public const RANDOM_SPLASH = "random.splash";
	public const RANDOM_SWIM = "random.swim";
	public const RANDOM_TOAST = "random.toast";
	public const RANDOM_TOTEM = "random.totem";
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
	public const VR_STUTTERTURN = "vr.stutterturn";

}