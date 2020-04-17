-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: mysql.ct8.pl
-- Czas generowania: 17 Kwi 2020, 19:55
-- Wersja serwera: 5.7.26-29-log
-- Wersja PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `m5988_stormy_oop`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `categoryId` int(11) NOT NULL,
  `categoryTitle` varchar(100) COLLATE utf8_bin NOT NULL,
  `categoryUrl` varchar(150) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `comments`
--

CREATE TABLE `comments` (
  `id` int(50) NOT NULL,
  `articleUrl` varchar(50) NOT NULL,
  `author` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `additionDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `comments`
--

INSERT INTO `comments` (`id`, `articleUrl`, `author`, `content`, `additionDate`) VALUES
(1, 'to-je-igor', 'Kacper', 'Świetny artykuł, pozdrawiam z rodzinką ', '2020-04-05 12:45:02'),
(2, 'to-je-igor', 'Kinga Kowalska', 'Piękny artykuł, przyjemnie się czyta. Pozdrawiam!', '2020-04-05 12:45:09'),
(3, 'to-je-igor', 'Kinga Kowalska', 'Piękny artykuł, przyjemnie się czyta. Pozdrawiam!', '2020-04-05 12:45:17'),
(4, 'igor-to-je-dopeiro-kulturysta', 'Budzę się piękniejsza', 'Będzie karnawał w Wenecji woda i kanalizacja wybuchła w domu', '2020-04-09 13:08:47'),
(5, 'igor-to-je-dopeiro-kulturysta', 'Nosu Szczepański ', 'Nie będę już pisał głupich komentarzy. Przepraszam!', '2020-04-09 13:12:52'),
(6, 'to-je-igor', 'Geniusz ', 'Brawo dla mnie!', '2020-04-09 13:19:15');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `menu`
--

CREATE TABLE `menu` (
  `optionId` int(11) NOT NULL,
  `optionOrder` int(11) NOT NULL,
  `visibleName` varchar(50) COLLATE utf8_bin NOT NULL,
  `destination` varchar(100) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `news`
--

CREATE TABLE `news` (
  `news_id` int(255) NOT NULL,
  `title` mediumtext NOT NULL,
  `photo` text NOT NULL,
  `content` longtext NOT NULL,
  `articleUrl` varchar(90) NOT NULL,
  `publicationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category` varchar(999) NOT NULL,
  `additionalCategory` varchar(900) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `news`
--

INSERT INTO `news` (`news_id`, `title`, `photo`, `content`, `articleUrl`, `publicationDate`, `category`, `additionalCategory`) VALUES
(3, 'Igorek', '29.03.2020 (20).jpeg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla scelerisque eros eu tellus egestas, vitae ullamcorper neque ultricies. Mauris congue viverra imperdiet. In nisl diam, ultrices eget molestie eu, convallis dapibus ipsum. Morbi finibus consectetur mauris, at egestas purus vulputate a. Donec non nisl luctus orci bibendum ornare. Morbi fringilla pellentesque ultrices. Phasellus imperdiet, felis at consequat rhoncus, enim sem semper est, non consequat lectus tellus ultrices ligula. Sed posuere nibh ut urna finibus, quis fringilla erat iaculis. Vestibulum sed molestie massa. Aliquam viverra neque at ex dignissim, at blandit leo laoreet. Etiam a leo ac lorem tincidunt efficitur. Vestibulum sagittis purus pretium purus egestas, et tempor ante ultricies.\r\n\r\nQuisque ligula neque, auctor auctor pretium vel, dictum quis leo. Ut elit lorem, commodo bibendum laoreet et, auctor a tortor. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nam ultricies nec dui vitae molestie. Ut sagittis quam libero, quis viverra ex condimentum nec. Nulla et urna id enim tempor finibus. Sed accumsan dolor sit amet ligula egestas vulputate. Suspendisse auctor dignissim ex eget semper. Sed fermentum laoreet nisl ac lacinia. Phasellus eget scelerisque dolor, ac vulputate dolor. Mauris efficitur ac metus eu placerat. Nunc volutpat luctus risus nec molestie. In ut malesuada ex. Vivamus fringilla imperdiet maximus. Donec ac volutpat arcu, in sagittis dolor. Maecenas accumsan augue vel purus pellentesque interdum.\r\n\r\nMauris nulla quam, rhoncus at lacinia vel, posuere vel justo. Aliquam ac risus ac orci suscipit interdum sagittis vitae ipsum. Donec nec ipsum in justo euismod consectetur. Nulla ut nibh vitae orci elementum imperdiet a vel metus. Quisque accumsan dignissim aliquet. Nunc dolor justo, scelerisque vitae nunc id, varius venenatis nunc. Fusce tempus, est id ullamcorper porta, urna elit pharetra nunc, et fringilla dolor nisl eget orci. Curabitur pulvinar vehicula sapien ac pellentesque. Nunc non fermentum tellus.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam ultricies quam magna, eu placerat dolor dignissim quis. Proin ac mollis massa. Fusce ornare elementum tellus dignissim efficitur. Cras consequat turpis ac nisi tempor, a sodales felis dapibus. Nunc eget elit eget ante tincidunt hendrerit. Proin eleifend in leo nec bibendum. Praesent mollis, arcu at luctus iaculis, mi magna luctus turpis, nec mollis felis nibh viverra mi. Fusce at tincidunt arcu, eu tempus arcu. Praesent pellentesque lacus et tempor elementum. Vivamus a ligula eros. Donec nec hendrerit eros. Morbi convallis pulvinar dolor.\r\n\r\nVivamus scelerisque in nisi in posuere. Sed quis aliquam felis, id vulputate eros. Aliquam finibus tortor nec felis porttitor, et bibendum tellus ullamcorper. Sed pretium ipsum sapien. Nunc sed tortor sit amet lorem mattis dignissim in non tellus. Phasellus consectetur urna sed laoreet pharetra. Suspendisse ac libero iaculis, accumsan diam eu, fringilla ante. Vivamus ac consectetur quam. Phasellus sodales pellentesque ornare. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.', 'to-je-igor', '2020-01-01 01:01:00', 'Igor', 'kacper'),
(4, 'Igor - Kulturysta dzisiejszych czasów', '29.03.2020 (37).jpeg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer varius cursus orci, sed scelerisque odio rhoncus vitae. Morbi ornare, nunc eleifend porta aliquet, risus enim ultricies arcu, ut condimentum nibh turpis at tortor. Ut at velit sit amet diam facilisis consectetur et in ex. Duis lacinia purus eu neque consequat lobortis. In convallis ullamcorper semper. Etiam sodales mollis libero, sagittis rutrum arcu efficitur vitae. Sed pretium turpis sodales, feugiat metus ac, hendrerit nunc. Aliquam et mauris lobortis lorem eleifend elementum a eu felis. Donec dolor ipsum, feugiat ut finibus vel, hendrerit eu urna. Nunc nisl metus, eleifend vel rhoncus at, aliquam ut libero. Fusce sit amet velit maximus, gravida nulla vel, ullamcorper elit. Donec ipsum lacus, pellentesque ut nisi eget, hendrerit posuere justo. Suspendisse porttitor leo eget hendrerit convallis. Cras vestibulum vestibulum nibh, ut imperdiet arcu. Mauris eget ipsum sagittis, convallis augue et, euismod velit.\r\n\r\nFusce eu dui vestibulum felis maximus dictum. Fusce vel vulputate leo, non vehicula felis. In ultricies pretium risus, at varius urna malesuada sit amet. Donec non mi ac eros tincidunt scelerisque sit amet vitae urna. Vivamus sapien dolor, faucibus ut tellus in, congue ultricies purus. Mauris sed ligula non libero venenatis pellentesque ac a quam. Nam eget augue ut sapien posuere elementum.\r\n\r\nUt non urna leo. Nunc sed vehicula nunc, pharetra rutrum turpis. Fusce vitae nunc quam. Suspendisse potenti. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed viverra eu ante id luctus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Cras nisl urna, gravida vitae ultricies eget, pharetra aliquam felis. Proin lacinia ullamcorper faucibus. Aliquam id hendrerit tellus, ac sagittis leo. Etiam vitae consectetur purus, non ultricies libero. Nullam vel felis vel justo feugiat aliquam quis a nulla.\r\n\r\nSuspendisse tincidunt, quam at egestas rhoncus, magna nunc venenatis justo, vitae consequat felis magna id magna. Donec ultrices tincidunt mi at ornare. Nullam molestie fringilla massa. Morbi dignissim lacus id sem suscipit feugiat. Vivamus non varius justo, nec blandit urna. Quisque nec massa nisl. In ex odio, facilisis ut vestibulum vitae, cursus porta risus.\r\n\r\nCurabitur mattis euismod turpis eu blandit. Duis tempor, tellus et faucibus auctor, ex urna congue justo, quis dictum diam erat eu augue. Donec nec molestie est. Sed laoreet iaculis ligula, non congue nisl pharetra et. Quisque tincidunt neque interdum turpis placerat, ut mattis dui porta. Curabitur et congue metus, non sollicitudin elit. Cras arcu felis, pharetra maximus rhoncus vehicula, tincidunt a sem. Nulla molestie erat id cursus finibus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla non nibh convallis, dapibus felis eget, gravida ante.\r\n\r\nInteger sollicitudin ex in dolor pretium hendrerit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Quisque justo nulla, fermentum et arcu vitae, mollis vehicula metus. Nam nec augue nec lacus interdum elementum et nec ipsum. Curabitur eget est justo. Aliquam non tortor tempor neque porttitor euismod. Pellentesque ac arcu eget risus porttitor aliquam. Nulla in mauris vehicula, malesuada ipsum quis, porttitor massa. Duis ullamcorper, tortor ut elementum vehicula, nulla enim hendrerit risus, a blandit sapien ligula id nibh. Integer vel finibus leo. Nam hendrerit, nibh sit amet finibus convallis, libero lorem feugiat eros, a vehicula augue velit vel justo. Sed efficitur turpis eu dictum fermentum.\r\n\r\nUt luctus accumsan quam rutrum volutpat. Vestibulum mattis turpis a tellus tincidunt pharetra. Aliquam non neque non leo congue cursus non viverra felis. Ut viverra ultrices massa, nec lacinia dolor rutrum id. Donec condimentum tortor et mauris suscipit fringilla. Maecenas rhoncus rhoncus leo ut porttitor. Nullam laoreet cursus lacus molestie tristique. Etiam vel accumsan odio. Morbi scelerisque, diam eget bibendum volutpat, erat nisl viverra magna, eu pretium purus neque ut arcu. Quisque libero dolor, euismod eget convallis sit amet, ultricies vel magna. Sed sagittis congue justo a venenatis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vestibulum quis libero sed justo mollis vehicula a ut ligula. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Phasellus tempor enim at mi finibus, ac pulvinar libero commodo.\r\n\r\nNam varius eros in consectetur lacinia. Praesent ac sollicitudin nulla. Curabitur et accumsan ligula. Vestibulum tristique mauris ut sagittis ultricies. Suspendisse molestie, mi quis dignissim ornare, arcu mi tristique nisl, quis vestibulum sapien sem sit amet mi. Curabitur est massa, bibendum semper lacinia sit amet, varius vitae nisi. Donec sagittis urna dui, eu feugiat urna tempus sit amet. Mauris tempus hendrerit vestibulum. Nulla vitae venenatis ipsum. Nulla a facilisis turpis, non interdum nisl. Maecenas vulputate consectetur eros, vitae aliquet turpis eleifend lobortis. Suspendisse vulputate est ac tempus sodales.\r\n\r\nCurabitur dapibus nisi eget magna consequat, ac lobortis urna tristique. Duis ornare lacinia erat, in tristique orci aliquet id. Duis blandit augue in sollicitudin semper. Nulla facilisi. Curabitur at cursus ligula. Cras non quam eu mi porttitor congue. Praesent tristique dolor sit amet elit vulputate, sit amet tincidunt urna malesuada. Curabitur dignissim augue arcu, sed laoreet sem mattis vitae. Sed accumsan, erat ac pulvinar ornare, magna urna bibendum elit, vel sodales odio mauris eu augue.\r\n\r\nDuis rutrum tempor mollis. In hac habitasse platea dictumst. Maecenas vel facilisis nisi. Fusce tincidunt erat vitae est sagittis, at aliquet justo aliquet. Cras scelerisque ultricies erat id pulvinar. Mauris sed sem at purus vulputate vestibulum. Duis eget tempus metus. Sed volutpat mollis mollis. Vestibulum sodales odio nisi, sed fermentum mauris elementum ac. Nulla luctus magna quis neque auctor vulputate. Donec odio ligula, consequat sit amet mauris at, elementum condimentum leo. Donec vitae massa ultricies, varius dolor eget, pharetra felis. Etiam porta efficitur tincidunt.\r\n\r\nIn hac habitasse platea dictumst. Phasellus vel lorem nibh. Curabitur porta vulputate nunc vitae accumsan. Quisque quis nisi augue. Nunc vitae ligula lobortis, vulputate arcu id, malesuada ex. Nunc pretium imperdiet massa sagittis semper. Donec consectetur tellus et nisi tristique iaculis. Nunc ac blandit purus.\r\n\r\nVestibulum fringilla dui eget ultrices ornare. Duis molestie mi vel luctus bibendum. Nullam lacus magna, viverra at odio eu, gravida pellentesque ligula. Donec eu tellus mattis sem semper tempus ac quis massa. Nulla hendrerit sollicitudin leo, sed malesuada odio facilisis scelerisque. Vestibulum ut vulputate leo. Suspendisse eget consequat mauris, vitae condimentum sapien. Maecenas imperdiet ex quis risus posuere malesuada. Curabitur mi mauris, elementum quis dolor et, malesuada eleifend massa. Donec non dapibus risus.\r\n\r\nSuspendisse rutrum iaculis magna, ut elementum ipsum feugiat quis. In vulputate tincidunt diam, et tincidunt justo auctor vel. Donec lacus felis, posuere id arcu vitae, rutrum feugiat sapien. Nullam ligula nibh, imperdiet sed lobortis fermentum, cursus sed enim. Mauris scelerisque tincidunt sagittis. Suspendisse potenti. Maecenas lacinia urna eget risus bibendum, sed placerat libero placerat. Sed varius tortor nulla, vel vulputate enim volutpat ut. Sed mollis nibh et lectus tincidunt tempus. Ut dui arcu, interdum non purus quis, consequat feugiat purus. Ut tempor leo ac arcu feugiat aliquet.\r\n\r\nSed sit amet tortor neque. Nam maximus lorem est, sit amet gravida eros mattis ac. Vivamus sed magna nec diam porta vulputate non sed quam. Pellentesque fringilla imperdiet massa, et semper leo placerat non. Nam eu felis tellus. Nam augue odio, dapibus at mi non, iaculis malesuada magna. Fusce ut dolor orci. Integer facilisis lectus risus, in posuere arcu finibus sit amet. Etiam laoreet, diam sit amet hendrerit finibus, velit lacus fermentum sem, ac placerat eros eros at orci.\r\n\r\nCurabitur porta pellentesque eros, ac gravida magna aliquam vel. Nullam a aliquet turpis, in molestie quam. Donec justo mi, placerat nec elementum at, posuere vel nibh. Quisque ut diam ultrices, venenatis massa vel, facilisis libero. Donec vestibulum pretium est et posuere. Aliquam egestas neque ac rutrum posuere. Etiam imperdiet porta urna, id pellentesque tortor. Proin consectetur, tortor sed dapibus mollis, augue nisl tincidunt mauris, ac placerat justo enim vel est. Nullam suscipit rhoncus pharetra. Vivamus ex turpis, elementum nec orci et, varius feugiat ipsum. Proin dictum risus eu tellus rhoncus, eget sollicitudin urna commodo. Aliquam aliquam lectus a dapibus rutrum. Vivamus iaculis posuere nisl, faucibus imperdiet ipsum pellentesque id.', 'igor-to-je-dopeiro-kulturysta', '2020-04-05 10:43:00', 'Igor', 'kacper'),
(5, 'Najprzystojniejszy mężczyzna na świecie - Igor Sosnowicz', 'P1010195_p2.jpeg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer varius cursus orci, sed scelerisque odio rhoncus vitae. Morbi ornare, nunc eleifend porta aliquet, risus enim ultricies arcu, ut condimentum nibh turpis at tortor. Ut at velit sit amet diam facilisis consectetur et in ex. Duis lacinia purus eu neque consequat lobortis. In convallis ullamcorper semper. Etiam sodales mollis libero, sagittis rutrum arcu efficitur vitae. Sed pretium turpis sodales, feugiat metus ac, hendrerit nunc. Aliquam et mauris lobortis lorem eleifend elementum a eu felis. Donec dolor ipsum, feugiat ut finibus vel, hendrerit eu urna. Nunc nisl metus, eleifend vel rhoncus at, aliquam ut libero. Fusce sit amet velit maximus, gravida nulla vel, ullamcorper elit. Donec ipsum lacus, pellentesque ut nisi eget, hendrerit posuere justo. Suspendisse porttitor leo eget hendrerit convallis. Cras vestibulum vestibulum nibh, ut imperdiet arcu. Mauris eget ipsum sagittis, convallis augue et, euismod velit.<br><br>\r\n\r\n\r\n\r\n\r\nFusce eu dui vestibulum felis maximus dictum. Fusce vel vulputate leo, non vehicula felis. In ultricies pretium risus, at varius urna malesuada sit amet. Donec non mi ac eros tincidunt scelerisque sit amet vitae urna. Vivamus sapien dolor, faucibus ut tellus in, congue ultricies purus. Mauris sed ligula non libero venenatis pellentesque ac a quam. Nam eget augue ut sapien posuere elementum.\r\n\r\nUt non urna leo. Nunc sed vehicula nunc, pharetra rutrum turpis. Fusce vitae nunc quam. Suspendisse potenti. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed viverra eu ante id luctus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Cras nisl urna, gravida vitae ultricies eget, pharetra aliquam felis. Proin lacinia ullamcorper faucibus. Aliquam id hendrerit tellus, ac sagittis leo. Etiam vitae consectetur purus, non ultricies libero. Nullam vel felis vel justo feugiat aliquam quis a nulla.\r\n\r\nSuspendisse tincidunt, quam at egestas rhoncus, magna nunc venenatis justo, vitae consequat felis magna id magna. Donec ultrices tincidunt mi at ornare. Nullam molestie fringilla massa. Morbi dignissim lacus id sem suscipit feugiat. Vivamus non varius justo, nec blandit urna. Quisque nec massa nisl. In ex odio, facilisis ut vestibulum vitae, cursus porta risus.\r\n\r\nCurabitur mattis euismod turpis eu blandit. Duis tempor, tellus et faucibus auctor, ex urna congue justo, quis dictum diam erat eu augue. Donec nec molestie est. Sed laoreet iaculis ligula, non congue nisl pharetra et. Quisque tincidunt neque interdum turpis placerat, ut mattis dui porta. Curabitur et congue metus, non sollicitudin elit. Cras arcu felis, pharetra maximus rhoncus vehicula, tincidunt a sem. Nulla molestie erat id cursus finibus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla non nibh convallis, dapibus felis eget, gravida ante.\r\n\r\nInteger sollicitudin ex in dolor pretium hendrerit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Quisque justo nulla, fermentum et arcu vitae, mollis vehicula metus. Nam nec augue nec lacus interdum elementum et nec ipsum. Curabitur eget est justo. Aliquam non tortor tempor neque porttitor euismod. Pellentesque ac arcu eget risus porttitor aliquam. Nulla in mauris vehicula, malesuada ipsum quis, porttitor massa. Duis ullamcorper, tortor ut elementum vehicula, nulla enim hendrerit risus, a blandit sapien ligula id nibh. Integer vel finibus leo. Nam hendrerit, nibh sit amet finibus convallis, libero lorem feugiat eros, a vehicula augue velit vel justo. Sed efficitur turpis eu dictum fermentum.\r\n\r\nUt luctus accumsan quam rutrum volutpat. Vestibulum mattis turpis a tellus tincidunt pharetra. Aliquam non neque non \r\n\r\n\r\n\r\n\r\nleo congue cursus non viverra felis. Ut viverra ultrices massa, nec lacinia dolor rutrum id. Donec condimentum tortor et mauris suscipit fringilla. Maecenas rhoncus rhoncus leo ut porttitor. Nullam laoreet cursus lacus molestie tristique. Etiam vel accumsan odio. Morbi scelerisque, diam eget bibendum volutpat, erat nisl viverra magna, eu pretium purus neque ut arcu. Quisque libero dolor, euismod eget convallis sit amet, ultricies vel magna. Sed sagittis congue justo a venenatis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vestibulum quis libero sed justo mollis vehicula a ut ligula. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Phasellus tempor enim at mi finibus, ac pulvinar libero commodo.\r\n\r\nNam varius eros in consectetur lacinia. Praesent ac sollicitudin nulla. Curabitur et accumsan ligula. Vestibulum tristique mauris ut sagittis ultricies. Suspendisse molestie, mi quis dignissim ornare, arcu mi tristique nisl, quis vestibulum sapien sem sit amet mi. Curabitur est massa, bibendum semper lacinia sit amet, varius vitae nisi. Donec sagittis urna dui, eu feugiat urna tempus sit amet. Mauris tempus hendrerit vestibulum. Nulla vitae venenatis ipsum. Nulla a facilisis turpis, non interdum nisl. Maecenas vulputate consectetur eros, vitae aliquet turpis eleifend lobortis. Suspendisse vulputate est ac tempus sodales.\r\n\r\nCurabitur dapibus nisi eget magna consequat, ac lobortis urna tristique. Duis ornare lacinia erat, in tristique orci aliquet id. Duis blandit augue in sollicitudin semper. Nulla facilisi. Curabitur at cursus ligula. Cras non quam eu mi porttitor congue. Praesent tristique dolor sit amet elit vulputate, sit amet tincidunt urna malesuada. Curabitur dignissim augue arcu, sed laoreet sem mattis vitae. Sed accumsan, erat ac pulvinar ornare, magna urna bibendum elit, vel sodales odio mauris eu augue.\r\n\r\nDuis rutrum tempor mollis. In hac habitasse platea dictumst. Maecenas vel facilisis nisi. Fusce tincidunt erat vitae est sagittis, at aliquet justo aliquet. Cras scelerisque ultricies erat id pulvinar. Mauris sed sem at purus vulputate vestibulum. Duis eget tempus metus. Sed volutpat mollis mollis. Vestibulum sodales odio nisi, sed fermentum mauris elementum ac. Nulla luctus magna quis neque auctor vulputate. Donec odio ligula, consequat sit amet mauris at, elementum condimentum leo. Donec vitae massa ultricies, varius dolor eget, pharetra felis. Etiam porta efficitur tincidunt.\r\n\r\nIn hac habitasse platea dictumst. Phasellus vel lorem nibh. Curabitur porta vulputate nunc vitae accumsan. Quisque quis nisi augue. Nunc vitae ligula lobortis, vulputate arcu id, malesuada ex. Nunc pretium imperdiet massa sagittis semper. Donec consectetur tellus et nisi tristique iaculis. Nunc ac blandit purus.\r\n\r\nVestibulum fringilla dui eget ultrices ornare. Duis molestie mi vel luctus bibendum. Nullam lacus magna, viverra at odio eu, gravida pellentesque ligula. Donec eu tellus mattis sem semper tempus ac quis massa. Nulla hendrerit sollicitudin leo, sed malesuada odio facilisis scelerisque. Vestibulum ut vulputate leo. Suspendisse eget consequat mauris, vitae condimentum sapien. Maecenas imperdiet ex quis risus posuere malesuada. Curabitur mi mauris, elementum quis dolor et, malesuada eleifend massa. Donec non dapibus risus.\r\n\r\nSuspendisse rutrum iaculis magna, ut elementum ipsum feugiat quis. In vulputate tincidunt diam, et tincidunt justo auctor vel. Donec lacus felis, posuere id arcu vitae, rutrum feugiat sapien. Nullam ligula nibh, imperdiet sed lobortis fermentum, cursus sed enim. Mauris scelerisque tincidunt sagittis. Suspendisse potenti. Maecenas lacinia urna eget risus bibendum, sed placerat libero placerat. Sed varius tortor nulla, vel vulputate enim volutpat ut. Sed mollis nibh et lectus tincidunt tempus. Ut dui arcu, interdum non purus quis, consequat feugiat purus. Ut tempor leo ac arcu feugiat aliquet.\r\n\r\nSed sit amet tortor neque. Nam maximus lorem est, sit amet gravida eros mattis ac. Vivamus sed magna nec diam porta vulputate non sed quam. Pellentesque fringilla imperdiet massa, et semper leo placerat non. Nam eu felis tellus. Nam augue odio, dapibus at mi non, iaculis malesuada magna. Fusce ut dolor orci. Integer facilisis lectus risus, in posuere arcu finibus sit amet. Etiam laoreet, diam sit amet hendrerit finibus, velit lacus fermentum sem, ac placerat eros eros at orci.\r\n\r\nCurabitur porta pellentesque eros, ac gravida magna aliquam vel. Nullam a aliquet turpis, in molestie quam. Donec justo mi, placerat nec elementum at, posuere vel nibh. Quisque ut diam ultrices, venenatis massa vel, facilisis libero. Donec vestibulum pretium est et posuere. Aliquam egestas neque ac rutrum posuere. Etiam imperdiet porta urna, id pellentesque tortor. Proin consectetur, tortor sed dapibus mollis, augue nisl tincidunt mauris, ac placerat justo enim vel est. Nullam suscipit rhoncus pharetra. Vivamus ex turpis, elementum nec orci et, varius feugiat ipsum. Proin dictum risus eu tellus rhoncus, eget sollicitudin urna commodo. Aliquam aliquam lectus a dapibus rutrum. Vivamus iaculis posuere nisl, faucibus imperdiet ipsum pellentesque id.', 'nvnvbnbn', '0000-00-00 00:00:00', 'Igor', 'kacper');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8_bin NOT NULL,
  `url` varchar(500) COLLATE utf8_bin NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`categoryId`),
  ADD UNIQUE KEY `categoryUrl` (`categoryUrl`);

--
-- Indeksy dla tabeli `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`optionId`);

--
-- Indeksy dla tabeli `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`),
  ADD UNIQUE KEY `articleUrl` (`articleUrl`);

--
-- Indeksy dla tabeli `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla tabel zrzutów
--

--
-- AUTO_INCREMENT dla tabeli `categories`
--
ALTER TABLE `categories`
  MODIFY `categoryId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT dla tabeli `menu`
--
ALTER TABLE `menu`
  MODIFY `optionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT dla tabeli `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
