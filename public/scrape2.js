const puppeteer = require('puppeteer');
var HTMLParser = require('node-html-parser');
const cheerio = require('cheerio');

var args = process.argv;
var postcode = args[2];

(async () => {

const browser = await puppeteer.launch({args: ['--no-sandbox', '--disable-setuid-sandbox']});
const page = await browser.newPage();

//page.on('console', console.log);

await page.goto('https://checker.ofcom.org.uk/broadband-coverage', {waitUntil: 'networkidle2', timeout: 0});
await page.click('[name="postcode"]');
await page.type('[name="postcode"]', postcode);

await page.screenshot({path: 'search.png'});
await Promise.all([
        page.click('.change-location'),
        page.waitForSelector(".details-page")
]);
//console.log('search done');
const cookies = await page.cookies();

await page.screenshot({path: 'results.png'});

//this loads the multiselect
let selector = '.multiselect__select';
await page.evaluate((selector) => document.querySelector(selector).click(), selector); 

const multiselect = await page.$('.multiselect');
const rect = await page.evaluate((header) => {
    const {top, left, bottom, right} = header.getBoundingClientRect();
    return {top, left, bottom, right};
}, multiselect);
//console.log(rect);
//but we can't seem to click it to get the addresses to load

await page.mouse.move(rect.top, rect.left);
await page.mouse.down();
await page.screenshot({path: 'results4.png'});
await page.mouse.up();
await page.waitFor(5000);
await page.$eval('.multiselect__element:nth-of-type(5)', elem => elem.click());
//tryin gto click it by nth position

await page.$eval('.multiselect', elem => elem.click());
page.waitForSelector(".multiselect__element:nth-of-type(5)")
await page.screenshot({path: 'results3.png'});

let selector1 = '.multiselect__element:nth-of-type(5)';
// does work
await page.evaluate((selector1) => document.querySelector(selector1).click(), selector1); 
await page.$eval('.multiselect__element:nth-of-type(5)', elem => elem.click());
var addresses = await page.evaluate(() => {

  document.querySelectorAll('.multiselect__element')
  var addresses = [];
  var addresses = Array.from(document.querySelectorAll('.multiselect__element'));
  for (var i = 0; i < addresses.length; i++) {
    if(i==5){
      
    }
  }
  return addresses.map(td => td.innerHTML)
  /*
  for (var i = 0; i < rows.length; i++) {
    speeds[i] = rows[i].innerHTMl;
  } 
  */   
  return addresses;
});

//console.log(addresses);
await page.keyboard.press('Enter'); 

await page.screenshot({path: 'results2.png'});

// Drops the mouse to another point
//await page.mouse.move(100, 100);
//await page.mouse.up();

//await page.select('#telCountryInput', 'my-value')
//await page.type('.multiselect__input', '1, PRICE COURT, SHOBNALL ROAD', {delay: 20});
//await page.screenshot({path: 'results2.png'});

var speeds = await page.evaluate(() => {
  var speeds = [];
  var rows = Array.from(document.querySelectorAll('.broadband-table tr'));
  return rows.map(td => td.innerHTML)
  /*
  for (var i = 0; i < rows.length; i++) {
    speeds[i] = rows[i].innerHTMl;
  } 
  */   
  return speeds;
});
//console.log(speeds);
  var root = '';
  var results = [];
  for (var i = 0; i < speeds.length; i++) {
    //for some reason none of the parsers work
    root = speeds[i];
    var a = root.split('<td');
    var resultsInner = [];

    if (typeof a[1] !== 'undefined'){
      var k = a[1].split('>');
      var l = k[1].split('<');
      results[l[0]] = [];
      
      if (typeof a[2] !== 'undefined'){
        var b = a[2].split('>');
        var f = b[1].split('<');
        resultsInner.push(f[0]);
      }
      if (typeof a[3] !== 'undefined'){
        var d = a[3].split('>');
        var g = d[1].split('<');
        //console.log(g[0]);
        resultsInner.push(g[0]);
      }
      if (typeof a[4] !== 'undefined'){
        if(a[4].indexOf('exclamation') !== -1 ){
          //console.log('exclamation');
          resultsInner.push('exclamation');
        }else{
          //console.log('no exclamation');
          resultsInner.push('no exclamation');
        }
      }
      results[l[0]] = resultsInner;
    }
  } 
  console.log(results);
await page.close();
    await browser.close();
})();

